import numpy as np
from tensorflow.keras.models import load_model
from tensorflow.keras.preprocessing import image

# Load trained model
model = load_model("skin_cancer_model.h5")

# Class labels
class_labels = {0: "Benign", 1: "Malignant"}

def predict_image(img_path):
    img = image.load_img(img_path, target_size=(150, 150))
    img_array = image.img_to_array(img)

    img_array = img_array / 255.0
    img_array = np.expand_dims(img_array, axis=0)

    prediction = model.predict(img_array)

    pred_class = int(prediction[0][0] > 0.5)
    confidence = prediction[0][0]

    print("Prediction:", class_labels[pred_class])
    print("Confidence:", confidence)


# Example
predict_image("C:/Users/HP/Downloads/1 (2).jpg")