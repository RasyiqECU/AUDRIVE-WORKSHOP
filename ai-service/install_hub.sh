#!/usr/bin/env bash
# Install tensorflow-hub ke venv WSL via uv (venv dibuat tanpa pip)
set -e
UV="$(command -v uv || echo "$HOME/.local/bin/uv")"
echo "uv: $UV"
"$UV" pip install --python /home/rasyiq/venvs/audrive/bin/python "setuptools<81" tensorflow-hub
/home/rasyiq/venvs/audrive/bin/python -c 'import tensorflow_hub as h; print("WSL-HUB-OK", h.__version__)'
