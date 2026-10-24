# TARUH FILE ANDA DI SINI
# ========================
#
# preprocessing/
#   └── scaler.pkl          ← Scaler dari sklearn (StandardScaler/MinMaxScaler)
#   └── label_encoder.pkl   ← LabelEncoder dari sklearn
#
# core/
#   └── model.pth           ← PyTorch full model (torch.save(model, "model.pth"))
#   └── model.keras         ← TensorFlow/Keras model (model.save("model.keras"))
#
# universal/
#   └── model.onnx          ← Hasil convert_model.py (REKOMENDASI untuk produksi)
#
# edge/
#   └── model.tflite        ← Hasil convert_model.py (untuk IoT/Edge device)
#
# Setelah menaruh file, ubah MODEL_BACKEND di .env:
#   MODEL_BACKEND=onnx      ← jika pakai .onnx (direkomendasikan)
#   MODEL_BACKEND=pytorch   ← jika pakai .pth
#   MODEL_BACKEND=keras     ← jika pakai .keras
#   MODEL_BACKEND=dummy     ← mode development tanpa model
