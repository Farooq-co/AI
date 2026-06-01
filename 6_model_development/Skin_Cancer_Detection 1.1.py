import warnings
warnings.filterwarnings('ignore', category=DeprecationWarning)
import tensorflow as tf
from tensorflow.keras.callbacks import EarlyStopping, ModelCheckpoint
from tensorflow.keras.preprocessing.image import ImageDataGenerator
from tensorflow.keras.models import Sequential
from tensorflow.keras.layers import Conv2D, Dropout, MaxPooling2D, Flatten, Dense


train_dir = "F:/data/train"
test_dir  = "F:/data/test"

IMG_SIZE = (150, 150)
BATCH_SIZE = 32


train_datagen = ImageDataGenerator(
    rescale=1./255,
    rotation_range=20,
    zoom_range=0.2,
    horizontal_flip=True
)

test_datagen = ImageDataGenerator(rescale=1./255)

train_data = train_datagen.flow_from_directory(
    train_dir,
    target_size=IMG_SIZE,
    batch_size=BATCH_SIZE,
    class_mode='binary'
)
# Note: For testing, we typically don't apply data augmentation, only rescaling.
test_data = test_datagen.flow_from_directory(
    test_dir,
    target_size=IMG_SIZE,
    batch_size=BATCH_SIZE,
    class_mode='binary'
)

# Build the CNN model
model = Sequential([
    Conv2D(32, (3,3), activation='relu', input_shape=(150,150,3)),
    MaxPooling2D(2,2),

    Conv2D(64, (3,3), activation='relu'),
    MaxPooling2D(2,2),

    Conv2D(128, (3,3), activation='relu'),
    MaxPooling2D(2,2),

    Flatten(),
    Dropout(0.5),
    Dense(128, activation='relu'),
    Dense(1, activation='sigmoid')  # Binary classification
])

# Compile the model
model.compile(
    optimizer='adam',
    loss='binary_crossentropy',
    metrics=['accuracy']
)
# Early stopping to halt training when a monitored metric has stopped improving.
early_stopping = EarlyStopping(monitor='val_loss', patience=3, verbose=1)

# Save the model after every epoch if the validation loss improves.
model_checkpoint = ModelCheckpoint('my_model.h5', monitor='val_loss', save_best_only=True, verbose=1)

# Train the model
history = model.fit(
    train_data,
    epochs=10,
    validation_data=test_data,
    callbacks=[early_stopping, model_checkpoint]
)
# save the model
model.save('my_model.h5')
# Evaluate the model on the test set
loss, acc = model.evaluate(test_data)
print("Test Accuracy:", acc)