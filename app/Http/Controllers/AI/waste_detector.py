import cv2
import numpy as np
from ultralytics import YOLO
from flask import Flask, request, jsonify
import tempfile
import os
from flask_cors import CORS
import base64
from PIL import Image
import io
import logging

# Configure logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

app = Flask(__name__)
CORS(app)  # Enable CORS for all routes

# Configuration
class Config:
    ALLOWED_EXTENSIONS = {'png', 'jpg', 'jpeg', 'gif', 'webp'}
    MAX_CONTENT_LENGTH = 16 * 1024 * 1024  # 16MB max file size
    CONFIDENCE_THRESHOLD = 0.5

# Get the absolute path to the models directory
BASE_DIR = os.path.abspath(os.path.join(os.path.dirname(__file__), '..', '..', '..', '..'))
MODEL_PATH = os.path.join(BASE_DIR, 'models', 'best.pt')

# Initialize model as None
model = None

def allowed_file(filename):
    return '.' in filename and filename.rsplit('.', 1)[1].lower() in Config.ALLOWED_EXTENSIONS

def load_model():
    global model
    try:
        logger.info(f"Loading model from: {MODEL_PATH}")
        if not os.path.exists(MODEL_PATH):
            logger.error(f"Model file not found at {MODEL_PATH}")
            return False
        
        model = YOLO(MODEL_PATH)
        logger.info("Model loaded successfully")
        logger.info(f"Model classes: {model.names}")
        return True
    except Exception as e:
        logger.error(f"Error loading model: {str(e)}")
        return False

def process_image(image):
    """Process image and return detections"""
    try:
        # Convert to RGB if needed
        if len(image.shape) == 2:  # grayscale
            image = cv2.cvtColor(image, cv2.COLOR_GRAY2RGB)
        elif image.shape[2] == 4:  # RGBA
            image = cv2.cvtColor(image, cv2.COLOR_RGBA2RGB)

        # Run inference
        results = model(image)
        
        # Process results
        detections = []
        for r in results:
            boxes = r.boxes
            for box in boxes:
                # Get box coordinates
                x1, y1, x2, y2 = map(int, box.xyxy[0])
                
                # Get class and confidence
                cls = int(box.cls[0])
                conf = float(box.conf[0])
                class_name = model.names[cls]
                
                if conf > Config.CONFIDENCE_THRESHOLD:
                    detections.append({
                        'class': class_name,
                        'confidence': round(conf * 100, 2),
                        'bbox': {
                            'x1': x1,
                            'y1': y1,
                            'x2': x2,
                            'y2': y2
                        }
                    })
        
        return detections, None
    except Exception as e:
        logger.error(f"Error during inference: {str(e)}")
        return None, str(e)

@app.route('/health', methods=['GET'])
def health_check():
    """Health check endpoint"""
    return jsonify({
        'status': 'healthy',
        'model_loaded': model is not None,
        'model_path': MODEL_PATH
    })

@app.route('/model/info', methods=['GET'])
def model_info():
    """Get model information"""
    if model is None:
        return jsonify({'error': 'Model not loaded'}), 500
    
    return jsonify({
        'model_type': 'YOLOv8',
        'classes': model.names,
        'confidence_threshold': Config.CONFIDENCE_THRESHOLD,
        'max_file_size': Config.MAX_CONTENT_LENGTH,
        'allowed_extensions': list(Config.ALLOWED_EXTENSIONS)
    })

@app.route('/detect', methods=['POST'])
def detect_waste():
    """Detect waste in uploaded image"""
    # Check if model is loaded
    if model is None and not load_model():
        return jsonify({'error': 'Model not initialized'}), 500

    # Check if image is in request
    if 'image' not in request.files and 'image_base64' not in request.json:
        return jsonify({
            'error': 'No image provided',
            'message': 'Please provide either a file upload or base64 image data'
        }), 400

    try:
        # Handle file upload
        if 'image' in request.files:
            image = request.files['image']
            if not allowed_file(image.filename):
                return jsonify({
                    'error': 'Invalid file type',
                    'allowed_types': list(Config.ALLOWED_EXTENSIONS)
                }), 400

            # Create temp file
            temp_dir = tempfile.mkdtemp()
            temp_path = os.path.join(temp_dir, 'temp_image.jpg')
            
            try:
                # Save and process image
                image.save(temp_path)
                img = cv2.imread(temp_path)
                if img is None:
                    raise ValueError("Failed to read image file")
                
                detections, error = process_image(img)
                
                if error:
                    return jsonify({'error': error}), 500

                return jsonify({
                    'success': True,
                    'detections': detections,
                    'message': f'Found {len(detections)} objects'
                })

            finally:
                # Cleanup
                if os.path.exists(temp_path):
                    os.remove(temp_path)
                if os.path.exists(temp_dir):
                    os.rmdir(temp_dir)

        # Handle base64 image
        elif 'image_base64' in request.json:
            try:
                # Decode base64 image
                image_data = base64.b64decode(request.json['image_base64'])
                nparr = np.frombuffer(image_data, np.uint8)
                img = cv2.imdecode(nparr, cv2.IMREAD_COLOR)
                
                if img is None:
                    raise ValueError("Failed to decode base64 image")
                
                detections, error = process_image(img)
                
                if error:
                    return jsonify({'error': error}), 500

                return jsonify({
                    'success': True,
                    'detections': detections,
                    'message': f'Found {len(detections)} objects'
                })

            except Exception as e:
                return jsonify({
                    'error': 'Invalid base64 image data',
                    'message': str(e)
                }), 400

    except Exception as e:
        logger.error(f"Error processing request: {str(e)}")
        return jsonify({
            'error': 'Internal server error',
            'message': str(e)
        }), 500

if __name__ == '__main__':
    # Try to load the model on startup
    if load_model():
        logger.info("Starting server with model loaded...")
        app.run(host='0.0.0.0', port=5000, debug=False)
    else:
        logger.error(f"Could not load model from {MODEL_PATH}") 