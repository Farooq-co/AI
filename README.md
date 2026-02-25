# AI and Data Science — Place Explorer

Run the CLI:

- Install dependencies: `poetry install`
- CLI usage (example):
  - `poetry run python main_1.py "Paris, France"`

Run the web UI (two ways):

1) Via env var (starts web UI after running the script):
   - Windows: `SET START_WEB_UI=1` then `poetry run python main_1.py`
   - Unix: `START_WEB_UI=1 poetry run python main_1.py`

2) Via console script (preferred):
   - After `poetry install`, run: `poetry run web-ui`

Notes:
- The web UI requires Flask; `poetry install` will install it.
- Pass API keys via env vars: `OPENWEATHER_API_KEY`, `YOUTUBE_API_KEY`, `GOOGLE_PLACES_API_KEY`.
- To run a script file explicitly: `poetry run python path\to\script.py` to avoid name collisions.
