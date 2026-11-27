# FILE MODEL
# ==========
#
# Dipakai saat MODEL_BACKEND=onnx (konfigurasi yang digunakan):
#
#   universal/
#     yamnet.onnx          <- embedding suara (YAMNet)
#     engine_gate.onnx     <- pemeriksa "mesin / bukan mesin"
#     model.onnx           <- klasifikasi 9 kelas kerusakan
#   preprocessing/
#     label_encoder.pkl    <- urutan label
#   labels.json            <- daftar kelas dan keterangan model
#
# Pendukung:
#   yamnet_tfhub/          <- cadangan YAMNet (TensorFlow SavedModel)
#   training_report.json   <- hasil pelatihan
#
# Pelatihan ulang: train_yamnet.py (klasifikasi) dan train_gate_v3.py (gate)
# di folder ai-service/.
#
# Backend lain yang didukung loader (filenya tidak disertakan):
#   pytorch  -> core/model.pth
#   keras    -> core/model.keras
#   xgboost  -> core/xgb_model.joblib
#
# Atur backend di ai-service/.env:
#   MODEL_BACKEND=onnx     <- direkomendasikan
#   MODEL_BACKEND=dummy    <- mode pengembangan tanpa model (bawaan .env.example)
