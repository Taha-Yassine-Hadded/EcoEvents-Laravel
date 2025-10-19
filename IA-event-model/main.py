# ============================================================================
# FASTAPI ECO EVENT CLASSIFICATION SERVICE
# File: main.py
# ============================================================================

from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel, Field
from typing import Optional, Dict, List
import pickle
import re
import uvicorn
from pathlib import Path
import traceback

# Initialize FastAPI app
app = FastAPI(
    title="Eco Event Classification API",
    description="API for classifying eco events into categories",
    version="1.0.0"
)

# Configure CORS
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  # In production, replace with your Laravel app URL
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# ============================================================================
# LOAD MODEL AND VECTORIZER
# ============================================================================

MODEL_PATH = Path("eco_event_classifier.pkl")
VECTORIZER_PATH = Path("tfidf_vectorizer.pkl")

model = None
vectorizer = None
model_info = {
    "loaded": False,
    "error": None,
    "model_type": None,
    "num_features": None,
    "labels": []
}

def load_models():
    """Load model and vectorizer with detailed error reporting"""
    global model, vectorizer, model_info
    
    try:
        # Check if files exist
        if not MODEL_PATH.exists():
            raise FileNotFoundError(f"Model file not found: {MODEL_PATH}")
        if not VECTORIZER_PATH.exists():
            raise FileNotFoundError(f"Vectorizer file not found: {VECTORIZER_PATH}")
        
        # Load model
        print("📦 Loading model...")
        with open(MODEL_PATH, 'rb') as f:
            model = pickle.load(f)
        print(f"✅ Model loaded: {type(model).__name__}")
        
        # Load vectorizer
        print("📦 Loading vectorizer...")
        with open(VECTORIZER_PATH, 'rb') as f:
            vectorizer = pickle.load(f)
        print(f"✅ Vectorizer loaded: {type(vectorizer).__name__}")
        
        # Verify vectorizer is fitted
        if not hasattr(vectorizer, 'vocabulary_'):
            raise ValueError("Vectorizer is not fitted (no vocabulary found)")
        
        # Store model info
        model_info["loaded"] = True
        model_info["model_type"] = type(model).__name__
        model_info["num_features"] = len(vectorizer.vocabulary_)
        model_info["labels"] = list(model.classes_) if hasattr(model, 'classes_') else []
        
        print(f"✅ Vectorizer has {len(vectorizer.vocabulary_)} features")
        print(f"✅ Model classes: {model_info['labels']}")
        print("✅ Model and vectorizer loaded successfully!")
        
    except FileNotFoundError as e:
        error_msg = f"Model files not found: {e}"
        print(f"❌ {error_msg}")
        model_info["error"] = error_msg
        model = None
        vectorizer = None
        
    except Exception as e:
        error_msg = f"Error loading model: {str(e)}\n{traceback.format_exc()}"
        print(f"❌ {error_msg}")
        model_info["error"] = error_msg
        model = None
        vectorizer = None

# Load models on startup
load_models()

# ============================================================================
# REQUEST/RESPONSE MODELS
# ============================================================================

class EventClassificationRequest(BaseModel):
    title: str = Field(..., description="Event title", min_length=1)
    description: Optional[str] = Field(None, description="Event description")
    category: Optional[str] = Field(None, description="Event category")
    keywords: Optional[str] = Field(None, description="Event keywords")

    class Config:
        json_schema_extra = {
            "example": {
                "title": "Nettoyage de plage",
                "description": "Venez ramasser les déchets sur la plage",
                "category": "Environnement",
                "keywords": "plage, déchets, ramassage"
            }
        }

class EventClassificationResponse(BaseModel):
    success: bool
    predicted_label: str
    confidence_scores: Dict[str, float]
    top_confidence: float
    input: Dict[str, str]

class BatchClassificationRequest(BaseModel):
    events: List[EventClassificationRequest]

class HealthResponse(BaseModel):
    status: str
    model_loaded: bool
    version: str
    model_info: Dict

# ============================================================================
# HELPER FUNCTIONS
# ============================================================================

def preprocess_text(text: str) -> str:
    """Clean and normalize text (must match training preprocessing)"""
    if not text:
        return ""
    
    # Convert to lowercase
    text = text.lower()
    
    # Remove special characters but keep French accents
    text = re.sub(r'[^\w\sàâäéèêëïîôùûüÿç-]', ' ', text)
    
    # Remove extra whitespace
    text = ' '.join(text.split())
    
    return text

def classify_event(title: str, description: str = "", category: str = "", keywords: str = "") -> Dict:
    """
    Classify an event and return prediction with confidence scores
    """
    if model is None or vectorizer is None:
        raise HTTPException(
            status_code=503,
            detail={
                "error": "Model not loaded",
                "message": "Model files are missing or failed to load",
                "model_info": model_info
            }
        )
    
    try:
        # Combine all text fields (same as training)
        combined_text = f"{title} {description} {category} {keywords}"
        processed_text = preprocess_text(combined_text)
        
        # Verify we have some text
        if not processed_text.strip():
            raise ValueError("No valid text content after preprocessing")
        
        # Transform text to TF-IDF features
        features = vectorizer.transform([processed_text])
        
        # Get prediction
        prediction = model.predict(features)[0]
        
        # Get confidence scores
        if hasattr(model, 'predict_proba'):
            probabilities = model.predict_proba(features)[0]
            confidence_scores = {
                label: float(prob) 
                for label, prob in zip(model.classes_, probabilities)
            }
            # Sort by confidence (descending)
            confidence_scores = dict(sorted(
                confidence_scores.items(), 
                key=lambda x: x[1], 
                reverse=True
            ))
            top_confidence = float(max(probabilities))
        else:
            confidence_scores = {prediction: 1.0}
            top_confidence = 1.0
        
        return {
            "predicted_label": prediction,
            "confidence_scores": confidence_scores,
            "top_confidence": top_confidence
        }
        
    except Exception as e:
        # Log the full error for debugging
        print(f"❌ Classification error: {str(e)}")
        print(traceback.format_exc())
        raise

# ============================================================================
# API ROUTES
# ============================================================================

@app.get("/", response_model=Dict)
async def root():
    """Root endpoint - API information"""
    return {
        "message": "Eco Event Classification API",
        "version": "1.0.0",
        "status": "active" if model_info["loaded"] else "model_not_loaded",
        "model_loaded": model_info["loaded"],
        "endpoints": {
            "/": "GET - API information",
            "/health": "GET - Health check",
            "/classify": "POST - Classify a single event",
            "/classify/batch": "POST - Classify multiple events",
            "/reload": "POST - Reload model and vectorizer",
            "/docs": "GET - Interactive API documentation",
            "/redoc": "GET - Alternative API documentation"
        },
        "labels": model_info["labels"]
    }

@app.get("/health", response_model=HealthResponse)
async def health_check():
    """Health check endpoint"""
    return {
        "status": "healthy" if model_info["loaded"] else "unhealthy",
        "model_loaded": model_info["loaded"],
        "version": "1.0.0",
        "model_info": model_info
    }

@app.post("/reload")
async def reload_models():
    """Reload model and vectorizer (useful after retraining)"""
    load_models()
    return {
        "success": model_info["loaded"],
        "message": "Models reloaded" if model_info["loaded"] else "Failed to reload models",
        "model_info": model_info
    }

@app.post("/classify", response_model=EventClassificationResponse)
async def classify_single_event(request: EventClassificationRequest):
    """
    Classify a single eco event
    
    - **title**: Event title (required)
    - **description**: Event description (optional)
    - **category**: Event category (optional)
    - **keywords**: Event keywords (optional)
    """
    try:
        result = classify_event(
            title=request.title,
            description=request.description or "",
            category=request.category or "",
            keywords=request.keywords or ""
        )
        
        return EventClassificationResponse(
            success=True,
            predicted_label=result["predicted_label"],
            confidence_scores=result["confidence_scores"],
            top_confidence=result["top_confidence"],
            input={
                "title": request.title,
                "description": request.description or "",
                "category": request.category or "",
                "keywords": request.keywords or ""
            }
        )
    
    except HTTPException:
        raise
    except Exception as e:
        print(f"❌ Classification error: {str(e)}")
        print(traceback.format_exc())
        raise HTTPException(
            status_code=500,
            detail=f"Classification error: {str(e)}"
        )

@app.post("/classify/batch")
async def classify_batch_events(request: BatchClassificationRequest):
    """
    Classify multiple events at once
    
    Accepts an array of events with the same structure as single classification
    """
    if not request.events:
        raise HTTPException(
            status_code=400,
            detail="Events array cannot be empty"
        )
    
    results = []
    
    for idx, event in enumerate(request.events):
        try:
            result = classify_event(
                title=event.title,
                description=event.description or "",
                category=event.category or "",
                keywords=event.keywords or ""
            )
            
            results.append({
                "index": idx,
                "success": True,
                "input": {
                    "title": event.title,
                    "description": event.description or ""
                },
                "prediction": result
            })
        
        except Exception as e:
            results.append({
                "index": idx,
                "success": False,
                "error": str(e),
                "input": {
                    "title": event.title,
                    "description": event.description or ""
                }
            })
    
    return {
        "success": True,
        "total_events": len(request.events),
        "results": results
    }

# ============================================================================
# MAIN
# ============================================================================

if __name__ == "__main__":
    print("\n" + "="*60)
    print("🚀 Starting Eco Event Classification API (FastAPI)")
    print("="*60)
    print("📍 API will be available at: http://localhost:8001")
    print("📚 Interactive Docs: http://localhost:8001/docs")
    print("📖 Alternative Docs: http://localhost:8001/redoc")
    
    if model_info["loaded"]:
        print(f"✅ Model: {model_info['model_type']}")
        print(f"✅ Features: {model_info['num_features']}")
        print(f"✅ Labels: {', '.join(model_info['labels'])}")
    else:
        print("⚠️  WARNING: Model not loaded!")
        print(f"   Error: {model_info['error']}")
        print("   Please run: python train_eco_model.py")
    
    print("="*60 + "\n")
    
    uvicorn.run(
        "main:app",
        host="0.0.0.0",
        port=8001,
        reload=True,
        log_level="info"
    )