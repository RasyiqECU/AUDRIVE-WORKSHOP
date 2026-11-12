#!/usr/bin/env bash
# Wrapper untuk menjalankan training/c script Python project ini di WSL2 dengan GPU.
# Pemakaian: wsl -d Ubuntu -- bash /mnt/c/Users/Pongo/Documents/audrive/ai-service/wsl_run.sh <script.py> [arg...]
set -e

VENV=/home/rasyiq/venvs/audrive

# TF dari pip perlu tahu lokasi lib CUDA/cuDNN yang di-install uv/pip
export LD_LIBRARY_PATH
LD_LIBRARY_PATH=$(find "$VENV/lib/python3.13/site-packages/nvidia" -maxdepth 2 -type d -name lib 2>/dev/null | tr '\n' ':')${LD_LIBRARY_PATH:-}
export LD_LIBRARY_PATH=${LD_LIBRARY_PATH%:}

exec "$VENV/bin/python" "$@"
