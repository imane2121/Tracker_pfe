import cv2
import numpy as np
from ultralytics import YOLO
from flask import Flask, request, jsonify
import tempfile
import os
from flask_cors import CORS

app = Flask(__name__)
CORS(app)  # Enable CORS for all routes

# Get the absolute path to the models directory
BASE_DIR = os.path.dirname(os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__)))))
MODEL_PATH = os.path.join(BASE_DIR, 'models', 'best.pt')

# Initialize model as None
model = None

def load_model():
    global model
    try:
        if os.path.exists(MODEL_PATH):
            model = YOLO(MODEL_PATH)
            print(f"Model loaded successfully from {MODEL_PATH}")
            return True
        else:
            print(f"Model file not found at {MODEL_PATH}")
            return False
    except Exception as e:
        print(f"Error loading model: {str(e)}")
        return False

def process_image(image_path):
    global model
    
    # Check if model is loaded
    if model is None:
        if not load_model():
            return None, "Model not initialized"

    # Read image
    frame = cv2.imread(image_path)
    if frame is None:
        return None, "Failed to read image"

    try:
        # Run YOLOv8 inference
        results = model(frame)
        
        # Process results
        detected_classes = []
        for r in results:
            boxes = r.boxes
            for box in boxes:
                # Get class name and confidence
                cls = int(box.cls[0])
                conf = float(box.conf[0])
                class_name = model.names[cls]
                
                if conf > 0.5:  # Confidence threshold
                    detected_classes.append({
                        'class': class_name,
                        'confidence': round(conf * 100, 2)
                    })

        return detected_classes, None
    except Exception as e:
        return None, f"Error during inference: {str(e)}"

@app.route('/detect', methods=['POST'])
def detect_waste():
    if 'image' not in request.files:
        return jsonify({'error': 'No image provided'}), 400

    image = request.files['image']
    
    # Create temporary directory if it doesn't exist
    temp_dir = tempfile.mkdtemp()
    temp_path = os.path.join(temp_dir, 'temp_image.jpg')
    
    try:
        # Save uploaded image
        image.save(temp_path)
        
        # Process the image
        detections, error = process_image(temp_path)
        
        if error:
            return jsonify({'error': error}), 500

        return jsonify({
            'success': True,
            'detections': detections
        })

    except Exception as e:
        return jsonify({'error': str(e)}), 500
    
    finally:
        # Clean up
        if os.path.exists(temp_path):
            os.remove(temp_path)
        if os.path.exists(temp_dir):
            os.rmdir(temp_dir)

if __name__ == '__main__':
    # Try to load the model on startup
    if load_model():
        print("Starting server with model loaded...")
        app.run(host='0.0.0.0', port=5000, debug=False)
    else:
        print("Error: Could not load model. Please ensure the model file exists at", MODEL_PATH) 