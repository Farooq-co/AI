import os
import json
import numpy as np
import pandas as pd
import matplotlib.pyplot as plt
import seaborn as sns
from datetime import datetime
from pathlib import Path

import tensorflow as tf
from tensorflow.keras import layers, models, callbacks, optimizers, regularizers
from tensorflow.keras.preprocessing.image import ImageDataGenerator
from sklearn.metrics import (
    accuracy_score, precision_score, recall_score, f1_score,
    confusion_matrix, classification_report, roc_auc_score, roc_curve, auc
)
from sklearn.utils.class_weight import compute_class_weight

# ============================================================================
# CONFIGURATION
# ============================================================================

CONFIG = {
    'MODEL_NAME': 'EfficientNetB1_Pneumonia_Detector',
    'INPUT_SIZE': (456, 456, 3),  # EfficientNetB1 optimal input size
    'BATCH_SIZE': 32,
    'EPOCHS': 100,
    'INITIAL_LR': 1e-3,
    'FINE_TUNE_LR': 1e-4,
    'FINE_TUNE_AT': 150,  # Unfreeze layers from this index
    'CLASS_WEIGHTS': True,
    'DATA_AUGMENTATION': True,
    'RANDOM_SEED': 42,
    'MODEL_SAVE_PATH': './models/',
    'RESULTS_SAVE_PATH': './results/',
}

# Set random seeds for reproducibility
np.random.seed(CONFIG['RANDOM_SEED'])
tf.random.set_seed(CONFIG['RANDOM_SEED'])

# Create directories
Path(CONFIG['MODEL_SAVE_PATH']).mkdir(parents=True, exist_ok=True)
Path(CONFIG['RESULTS_SAVE_PATH']).mkdir(parents=True, exist_ok=True)

print("="*80)
print(f"Medical AI: Pneumonia Detection using {CONFIG['MODEL_NAME']}")
print("="*80)

# ============================================================================
# DATA AUGMENTATION PIPELINE
# ============================================================================

def get_data_augmentation():
    """
    Create data augmentation pipeline for medical imaging.
    Medical images require careful augmentation to preserve diagnostic features.
    """
    augmentation = ImageDataGenerator(
        rotation_range=20,          # Slight rotations (doctors rotate X-rays)
        width_shift_range=0.15,     # Horizontal shifts
        height_shift_range=0.15,    # Vertical shifts
        zoom_range=0.2,             # Zoom variations
        brightness_range=[0.8, 1.2],# Lighting variations (scanner differences)
        horizontal_flip=True,       # X-rays can be flipped (bilateral symmetry)
        fill_mode='reflect',        # How to fill new pixels
        vertical_flip=False,        # DON'T flip vertically (breaks anatomy)
    )
    return augmentation

# ============================================================================
# MODEL CREATION WITH TRANSFER LEARNING
# ============================================================================

def create_transfer_learning_model(input_shape, num_classes=2):
    """
    Create a state-of-the-art medical imaging model using EfficientNetB1.
    
    Why EfficientNetB1 for Pneumonia Detection?
    ✓ Best accuracy-efficiency tradeoff for medical imaging
    ✓ Smaller than B4-B7, faster training/inference
    ✓ Proven on medical datasets (CheXpert, MIMIC-CXR)
    ✓ Handles 456x456 images efficiently
    ✓ Good for deployment on medical devices
    ✓ Fine-tuning friendly
    """
    
    print("\n[*] Building Transfer Learning Model...")
    print(f"[*] Using EfficientNetB1 pretrained on ImageNet")
    
    # Load pretrained model
    base_model = tf.keras.applications.EfficientNetB1(
        input_shape=input_shape,
        weights='imagenet',
        include_top=False,  # Remove top classification layers
        pooling='avg'       # Use average pooling instead of max
    )
    
    # Freeze all base model layers initially (transfer learning phase)
    base_model.trainable = False
    print(f"[✓] Base model loaded. Total layers: {len(base_model.layers)}")
    print(f"[✓] Freezing all layers for initial training phase")
    
    # Build custom classification head
    model = models.Sequential([
        layers.Input(shape=input_shape),
        
        # Preprocessing (EfficientNet expects preprocessed input)
        tf.keras.applications.efficientnet.preprocess_input,
        
        # Base model
        base_model,
        
        # Custom head for medical classification
        layers.BatchNormalization(momentum=0.9),
        layers.Dropout(0.5),
        
        layers.Dense(512, activation='relu', 
                    kernel_regularizer=regularizers.l2(1e-4)),
        layers.BatchNormalization(momentum=0.9),
        layers.Dropout(0.4),
        
        layers.Dense(256, activation='relu',
                    kernel_regularizer=regularizers.l2(1e-4)),
        layers.BatchNormalization(momentum=0.9),
        layers.Dropout(0.3),
        
        layers.Dense(128, activation='relu',
                    kernel_regularizer=regularizers.l2(1e-4)),
        layers.BatchNormalization(momentum=0.9),
        layers.Dropout(0.2),
        
        # Output layer
        layers.Dense(num_classes, 
                    activation='softmax' if num_classes > 2 else 'sigmoid')
    ])
    
    return model, base_model

# ============================================================================
# FINE-TUNING STRATEGY
# ============================================================================

def unfreeze_and_finetune(model, base_model, unfreeze_from_layer):
    """
    Unfreeze layers from a certain point for fine-tuning.
    This allows adaptation to medical imaging specifics while retaining
    learned ImageNet features.
    """
    print(f"\n[*] Fine-tuning strategy activated")
    print(f"[*] Unfreezing layers from index {unfreeze_from_layer}")
    
    # Freeze early layers (general features)
    for layer in base_model.layers[:unfreeze_from_layer]:
        layer.trainable = False
    
    # Unfreeze later layers (domain-specific features)
    for layer in base_model.layers[unfreeze_from_layer:]:
        layer.trainable = True
    
    trainable_count = sum([1 for layer in model.trainable_weights])
    print(f"[✓] Trainable parameters: {trainable_count}")
    
    return model

# ============================================================================
# COMPILE MODEL
# ============================================================================

def compile_model(model, learning_rate, num_classes=2):
    """Compile model with appropriate loss and optimizer for medical classification."""
    
    # Choose loss based on classification type
    if num_classes > 2:
        loss = 'categorical_crossentropy'
        metrics = ['accuracy']
    else:
        loss = 'binary_crossentropy'
        metrics = ['accuracy', tf.keras.metrics.AUC(name='auc')]
    
    optimizer = optimizers.Adam(learning_rate=learning_rate)
    
    model.compile(
        optimizer=optimizer,
        loss=loss,
        metrics=metrics
    )
    
    print(f"[✓] Model compiled")
    print(f"[✓] Loss: {loss}")
    print(f"[✓] Optimizer: Adam (lr={learning_rate})")
    
    return model

# ============================================================================
# DATA PREPARATION & CLASS WEIGHTS
# ============================================================================

def calculate_class_weights(y_train, num_classes):
    """
    Calculate class weights to handle imbalanced medical datasets.
    Pneumonia detection often has class imbalance (more normal than pneumonia).
    """
    
    # Convert one-hot to class indices if needed
    if len(y_train.shape) > 1:
        y_train_classes = np.argmax(y_train, axis=1)
    else:
        y_train_classes = (y_train > 0.5).astype(int)
    
    class_weights = compute_class_weight(
        'balanced',
        classes=np.unique(y_train_classes),
        y=y_train_classes
    )
    
    class_weight_dict = {i: weight for i, weight in enumerate(class_weights)}
    
    print(f"\n[*] Class weights (handling imbalance):")
    for class_idx, weight in class_weight_dict.items():
        print(f"    Class {class_idx}: {weight:.3f}")
    
    return class_weight_dict

# ============================================================================
# TRAINING FUNCTION
# ============================================================================

def train_model(model, train_data, val_data, class_weights, config):
    """
    Train model with callbacks for best practices.
    """
    
    print("\n[*] Setting up training callbacks...")
    
    # Early stopping (prevent overfitting)
    early_stop = callbacks.EarlyStopping(
        monitor='val_loss',
        patience=15,
        restore_best_weights=True,
        verbose=1
    )
    
    # Model checkpoint (save best model)
    timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
    checkpoint_path = f"{config['MODEL_SAVE_PATH']}{config['MODEL_NAME']}_{timestamp}.h5"
    model_checkpoint = callbacks.ModelCheckpoint(
        checkpoint_path,
        monitor='val_auc' if 'auc' in model.metrics_names else 'val_accuracy',
        save_best_only=True,
        mode='max',
        verbose=1
    )
    
    # Learning rate reduction
    reduce_lr = callbacks.ReduceLROnPlateau(
        monitor='val_loss',
        factor=0.5,
        patience=5,
        min_lr=1e-6,
        verbose=1
    )
    
    # TensorBoard logging
    log_dir = f"./logs/{timestamp}"
    tensorboard = callbacks.TensorBoard(
        log_dir=log_dir,
        histogram_freq=1
    )
    
    print(f"[✓] Callbacks configured")
    print(f"[✓] Best model will be saved to: {checkpoint_path}")
    
    print("\n[*] Starting training...")
    print("="*80)
    
    history = model.fit(
        train_data,
        validation_data=val_data,
        epochs=config['EPOCHS'],
        class_weight=class_weights if config['CLASS_WEIGHTS'] else None,
        callbacks=[early_stop, model_checkpoint, reduce_lr, tensorboard],
        verbose=1
    )
    
    print("="*80)
    print("[✓] Training completed!")
    
    return history, checkpoint_path

# ============================================================================
# EVALUATION METRICS
# ============================================================================

def evaluate_model(model, test_data, test_labels, num_classes=2):
    """
    Comprehensive evaluation with medical-relevant metrics.
    Recall is especially important in medical diagnosis (minimize false negatives).
    """
    
    print("\n[*] Evaluating model on test set...")
    
    # Predictions
    y_pred_proba = model.predict(test_data, verbose=0)
    
    if num_classes > 2:
        y_pred = np.argmax(y_pred_proba, axis=1)
        y_true = np.argmax(test_labels, axis=1)
    else:
        y_pred = (y_pred_proba > 0.5).astype(int).flatten()
        y_true = (test_labels > 0.5).astype(int).flatten()
    
    # Calculate metrics
    metrics_dict = {
        'accuracy': accuracy_score(y_true, y_pred),
        'precision': precision_score(y_true, y_pred, average='weighted', zero_division=0),
        'recall': recall_score(y_true, y_pred, average='weighted', zero_division=0),
        'f1': f1_score(y_true, y_pred, average='weighted', zero_division=0),
    }
    
    # AUC for binary classification
    if num_classes == 2:
        metrics_dict['auc'] = roc_auc_score(y_true, y_pred_proba)
    
    print("\n" + "="*80)
    print("EVALUATION RESULTS")
    print("="*80)
    print(f"Accuracy:  {metrics_dict['accuracy']:.4f}")
    print(f"Precision: {metrics_dict['precision']:.4f}")
    print(f"Recall:    {metrics_dict['recall']:.4f} (Important: catch all pneumonia cases)")
    print(f"F1-Score:  {metrics_dict['f1']:.4f}")
    if 'auc' in metrics_dict:
        print(f"AUC:       {metrics_dict['auc']:.4f}")
    print("="*80)
    
    # Confusion matrix
    cm = confusion_matrix(y_true, y_pred)
    print("\nConfusion Matrix:")
    print(cm)
    
    # Classification report
    print("\nDetailed Classification Report:")
    print(classification_report(y_true, y_pred))
    
    return metrics_dict, cm, y_true, y_pred, y_pred_proba

# ============================================================================
# VISUALIZATION
# ============================================================================

def plot_training_history(history, save_path):
    """Plot training and validation curves."""
    
    fig, axes = plt.subplots(1, 2, figsize=(14, 5))
    
    # Accuracy
    axes[0].plot(history.history['accuracy'], label='Training Accuracy')
    axes[0].plot(history.history['val_accuracy'], label='Validation Accuracy')
    axes[0].set_xlabel('Epoch')
    axes[0].set_ylabel('Accuracy')
    axes[0].set_title('Model Accuracy')
    axes[0].legend()
    axes[0].grid(True)
    
    # Loss
    axes[1].plot(history.history['loss'], label='Training Loss')
    axes[1].plot(history.history['val_loss'], label='Validation Loss')
    axes[1].set_xlabel('Epoch')
    axes[1].set_ylabel('Loss')
    axes[1].set_title('Model Loss')
    axes[1].legend()
    axes[1].grid(True)
    
    plt.tight_layout()
    plt.savefig(f"{save_path}training_history.png", dpi=300, bbox_inches='tight')
    print(f"[✓] Training history saved to {save_path}training_history.png")
    plt.close()

def plot_confusion_matrix(cm, save_path):
    """Plot confusion matrix."""
    
    plt.figure(figsize=(8, 6))
    sns.heatmap(cm, annot=True, fmt='d', cmap='Blues', 
                xticklabels=['Normal', 'Pneumonia'],
                yticklabels=['Normal', 'Pneumonia'])
    plt.ylabel('True Label')
    plt.xlabel('Predicted Label')
    plt.title('Confusion Matrix - Pneumonia Detection')
    plt.tight_layout()
    plt.savefig(f"{save_path}confusion_matrix.png", dpi=300, bbox_inches='tight')
    print(f"[✓] Confusion matrix saved to {save_path}confusion_matrix.png")
    plt.close()

def plot_roc_curve(y_true, y_pred_proba, save_path):
    """Plot ROC curve for binary classification."""
    
    fpr, tpr, _ = roc_curve(y_true, y_pred_proba)
    roc_auc = auc(fpr, tpr)
    
    plt.figure(figsize=(8, 6))
    plt.plot(fpr, tpr, color='darkorange', lw=2, label=f'ROC curve (AUC = {roc_auc:.3f})')
    plt.plot([0, 1], [0, 1], color='navy', lw=2, linestyle='--', label='Random Classifier')
    plt.xlim([0.0, 1.0])
    plt.ylim([0.0, 1.05])
    plt.xlabel('False Positive Rate')
    plt.ylabel('True Positive Rate')
    plt.title('ROC Curve - Pneumonia Detection')
    plt.legend(loc="lower right")
    plt.grid(True)
    plt.tight_layout()
    plt.savefig(f"{save_path}roc_curve.png", dpi=300, bbox_inches='tight')
    print(f"[✓] ROC curve saved to {save_path}roc_curve.png")
    plt.close()

# ============================================================================
# SAVE MODEL AND RESULTS
# ============================================================================

def save_model_and_results(model, history, metrics_dict, cm, config, checkpoint_path):
    """Save trained model and results."""
    
    timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
    
    # Save model in multiple formats
    print(f"\n[*] Saving model...")
    
    # SavedModel format (recommended)
    saved_model_path = f"{config['MODEL_SAVE_PATH']}{config['MODEL_NAME']}_{timestamp}"
    model.save(saved_model_path)
    print(f"[✓] Model saved (SavedModel format): {saved_model_path}")
    
    # Also save as .keras (newer Keras format)
    keras_path = f"{config['MODEL_SAVE_PATH']}{config['MODEL_NAME']}_{timestamp}.keras"
    model.save(keras_path)
    print(f"[✓] Model saved (.keras format): {keras_path}")
    
    # Save model configuration
    config_path = f"{config['RESULTS_SAVE_PATH']}model_config_{timestamp}.json"
    model_config = {
        'model_name': config['MODEL_NAME'],
        'input_size': config['INPUT_SIZE'],
        'architecture': 'EfficientNetB1 with Transfer Learning',
        'pretrained_weights': 'ImageNet',
        'timestamp': timestamp,
    }
    with open(config_path, 'w') as f:
        json.dump(model_config, f, indent=2)
    print(f"[✓] Model config saved: {config_path}")
    
    # Save metrics
    metrics_path = f"{config['RESULTS_SAVE_PATH']}metrics_{timestamp}.json"
    metrics_to_save = {k: float(v) for k, v in metrics_dict.items()}
    with open(metrics_path, 'w') as f:
        json.dump(metrics_to_save, f, indent=2)
    print(f"[✓] Metrics saved: {metrics_path}")
    
    # Save results summary
    results_path = f"{config['RESULTS_SAVE_PATH']}results_summary_{timestamp}.txt"
    with open(results_path, 'w') as f:
        f.write(f"Medical AI: Pneumonia Detection Results\n")
        f.write(f"Model: {config['MODEL_NAME']}\n")
        f.write(f"Timestamp: {timestamp}\n")
        f.write(f"\nEvaluation Metrics:\n")
        for metric, value in metrics_dict.items():
            f.write(f"  {metric}: {value:.4f}\n")
        f.write(f"\nConfusion Matrix:\n{cm}\n")
    print(f"[✓] Results summary saved: {results_path}")

# ============================================================================
# MAIN PIPELINE
# ============================================================================

def main():
    """
    Complete pipeline for training medical AI model.
    """
    
    print("\n[!] IMPORTANT: This is a template. You need to provide your dataset.")
    print("[!] Expected dataset structure:")
    print("    data/")
    print("    ├── train/")
    print("    │   ├── Normal/")
    print("    │   └── Pneumonia/")
    print("    ├── val/")
    print("    │   ├── Normal/")
    print("    │   └── Pneumonia/")
    print("    └── test/")
    print("        ├── Normal/")
    print("        └── Pneumonia/")
    
    # Check if data exists
    data_dir = './data'
    if not os.path.exists(data_dir):
        print(f"\n[!] Dataset not found at {data_dir}")
        print("[!] Creating dummy data for demonstration...")
        
        # This is for demonstration only
        import warnings
        warnings.warn("Using dummy data - replace with real dataset")
        
        # Create dummy datasets
        from tensorflow.keras.datasets import cifar10
        (x_train, y_train), (x_test, y_test) = cifar10.load_data()
        
        # Resize to 456x456
        from tensorflow.image import resize
        x_train = resize(x_train[:1000], [456, 456]).numpy()
        x_test = resize(x_test[:500], [456, 456]).numpy()
        
        # Create binary labels (dummy)
        y_train = (y_train[:1000] % 2).flatten()
        y_test = (y_test[:500] % 2).flatten()
        
        # Create validation split
        split = int(0.8 * len(x_train))
        x_val, y_val = x_train[split:], y_train[split:]
        x_train, y_train = x_train[:split], y_train[:split]
        
    else:
        print(f"\n[✓] Loading data from {data_dir}")
        # Load from ImageDataGenerator (for real dataset)
        # This is where you'd implement actual data loading
        
    # ========================================================================
    # MODEL CREATION
    # ========================================================================
    
    model, base_model = create_transfer_learning_model(
        input_shape=CONFIG['INPUT_SIZE'],
        num_classes=2
    )
    
    # Initial compilation (frozen layers)
    model = compile_model(model, learning_rate=CONFIG['INITIAL_LR'], num_classes=2)
    
    print("\n[*] Model Summary:")
    model.summary()
    
    # ========================================================================
    # DUMMY DATA PREPARATION (Replace with your real data)
    # ========================================================================
    
    # For demonstration - replace with real data loading
    try:
        print("\n[*] Preparing data...")
        
        # Dummy data
        x_train = np.random.rand(200, 456, 456, 3).astype(np.float32)
        y_train = np.random.randint(0, 2, (200,))
        
        x_val = np.random.rand(50, 456, 456, 3).astype(np.float32)
        y_val = np.random.randint(0, 2, (50,))
        
        x_test = np.random.rand(50, 456, 456, 3).astype(np.float32)
        y_test = np.random.randint(0, 2, (50,))
        
        # Normalize
        x_train = x_train / 255.0
        x_val = x_val / 255.0
        x_test = x_test / 255.0
        
        print("[✓] Data prepared (dummy data for demonstration)")
        
        # Calculate class weights
        class_weights = calculate_class_weights(y_train, num_classes=2)
        
        # ====================================================================
        # INITIAL TRAINING (Frozen base model)
        # ====================================================================
        
        print("\n" + "="*80)
        print("PHASE 1: INITIAL TRAINING (Frozen Base Model)")
        print("="*80)
        
        history1, checkpoint_path = train_model(
            model=model,
            train_data=(x_train, y_train),
            val_data=(x_val, y_val),
            class_weights=class_weights,
            config=CONFIG
        )
        
        # ====================================================================
        # FINE-TUNING (Unfreeze and retrain)
        # ====================================================================
        
        print("\n" + "="*80)
        print("PHASE 2: FINE-TUNING (Unfreezing Higher Layers)")
        print("="*80)
        
        model = unfreeze_and_finetune(
            model=model,
            base_model=base_model,
            unfreeze_from_layer=CONFIG['FINE_TUNE_AT']
        )
        
        # Recompile with lower learning rate
        model = compile_model(
            model=model,
            learning_rate=CONFIG['FINE_TUNE_LR'],
            num_classes=2
        )
        
        # Fine-tune training
        history2, checkpoint_path = train_model(
            model=model,
            train_data=(x_train, y_train),
            val_data=(x_val, y_val),
            class_weights=class_weights,
            config={**CONFIG, 'EPOCHS': 30}  # Fewer epochs for fine-tuning
        )
        
        # ====================================================================
        # EVALUATION
        # ====================================================================
        
        metrics_dict, cm, y_true, y_pred, y_pred_proba = evaluate_model(
            model=model,
            test_data=x_test,
            test_labels=y_test,
            num_classes=2
        )
        
        # ====================================================================
        # VISUALIZATION AND SAVING
        # ====================================================================
        
        print("\n[*] Generating visualizations...")
        plot_training_history(history2, CONFIG['RESULTS_SAVE_PATH'])
        plot_confusion_matrix(cm, CONFIG['RESULTS_SAVE_PATH'])
        plot_roc_curve(y_true, y_pred_proba.flatten(), CONFIG['RESULTS_SAVE_PATH'])
        
        save_model_and_results(
            model=model,
            history=history2,
            metrics_dict=metrics_dict,
            cm=cm,
            config=CONFIG,
            checkpoint_path=checkpoint_path
        )
        
        print("\n" + "="*80)
        print("[✓] TRAINING COMPLETED SUCCESSFULLY!")
        print("="*80)
        print(f"[✓] Model saved to: {CONFIG['MODEL_SAVE_PATH']}")
        print(f"[✓] Results saved to: {CONFIG['RESULTS_SAVE_PATH']}")
        
    except Exception as e:
        print(f"\n[!] Error during training: {str(e)}")
        import traceback
        traceback.print_exc()

# ============================================================================
# INFERENCE FUNCTION (For deployment)
# ============================================================================

def predict_pneumonia(image_path, model_path):
    """
    Make prediction on a single chest X-ray image.
    
    Usage:
        prediction, confidence = predict_pneumonia('path/to/xray.jpg', 'path/to/model')
    """
    
    # Load image
    img = tf.keras.preprocessing.image.load_img(image_path, target_size=(456, 456))
    img_array = tf.keras.preprocessing.image.img_to_array(img)
    img_array = tf.expand_dims(img_array, 0)
    img_array = img_array / 255.0
    
    # Load model
    model = tf.keras.models.load_model(model_path)
    
    # Predict
    prediction = model.predict(img_array)[0]
    
    if prediction.shape[0] == 1:  # Binary classification
        prob_pneumonia = prediction[0]
        diagnosis = "Pneumonia" if prob_pneumonia > 0.5 else "Normal"
        confidence = prob_pneumonia if prob_pneumonia > 0.5 else (1 - prob_pneumonia)
    else:  # Multi-class
        diagnosis = ["Normal", "Bacterial", "Viral"][np.argmax(prediction)]
        confidence = np.max(prediction)
    
    return diagnosis, float(confidence)

# ============================================================================

if __name__ == "__main__":
    main()
