import os
import sys
import argparse
import requests
import webbrowser
from time import sleep
# Optional Flask import moved to runtime (imported only when START_WEB_UI=1)
import pathlib
PYPROJECT_TOML = """[project]
name = "agentic-place-explorer"
version = "0.1.0"
description = "Show YouTube videos, weather, and nearby hotels for a place."
readme = "README.md"
requires-python = ">=3.8"
license = { text = "MIT" }
authors = [{ name = "agentic", email = "example@example.com" }]

dependencies = [
    "requests>=2.28",
    "Flask>=2.0",
]

[project.urls]
"Homepage" = "https://example.com"

[build-system]
requires = ["setuptools>=61.0", "wheel"]
build-backend = "setuptools.build_meta"
"""

def ensure_pyproject(auto_write=False):
        p = pathlib.Path("pyproject.toml")
        if p.exists():
                return
        if auto_write or os.getenv("WRITE_PYPROJECT") == "1":
                p.write_text(PYPROJECT_TOML, encoding="utf-8")

# write pyproject.toml automatically if env var set, otherwise no-op
ensure_pyproject(auto_write=False)
# main_1.py
# Agentic helper: given a place name, shows YouTube videos, current weather, and nearby hotels.
# Requires: requests
# Optional API keys via environment variables:
#   YOUTUBE_API_KEY, OPENWEATHER_API_KEY, GOOGLE_PLACES_API_KEY
# Usage: python main_1.py "Paris, France" [--open-videos] [--max 5]

import urllib.parse

USER_AGENT = "agentic-ai-example/1.0 (contact: example@example.com)"

def geocode(place):
    """Geocode a place using Nominatim.

    Supports environment variables:
      - NOMINATIM_EMAIL: added to the request params (recommended by Nominatim)
      - NOMINATIM_USER_AGENT: overrides User-Agent header
    Raises a RuntimeError with a helpful message if Nominatim returns 403 Forbidden.
    """
    url = "https://nominatim.openstreetmap.org/search"
    params = {"q": place, "format": "json", "limit": 1}
    email = os.getenv("NOMINATIM_EMAIL")
    if email:
        params["email"] = email
    headers = {"User-Agent": os.getenv("NOMINATIM_USER_AGENT", USER_AGENT)}

    r = requests.get(url, params=params, headers=headers, timeout=10)
    try:
        r.raise_for_status()
    except Exception as e:
        # Determine status code from exception (if it has a response) or from response object
        status = None
        resp = getattr(e, 'response', None)
        if resp is not None:
            status = getattr(resp, 'status_code', None)
        else:
            status = getattr(r, 'status_code', None)

        if status == 403:
            raise RuntimeError(
                "Geocoding blocked (403). Set NOMINATIM_EMAIL or use a different geocoding provider; public Nominatim has usage limits."
            ) from e
        raise

    data = r.json()
    if not data:
        return None
    return {"lat": float(data[0]["lat"]), "lon": float(data[0]["lon"]), "display_name": data[0]["display_name"]}

def get_weather(lat, lon, api_key):
    if not api_key:
        return None
    url = "https://api.openweathermap.org/data/2.5/weather"
    params = {"lat": lat, "lon": lon, "appid": api_key, "units": "metric"}
    r = requests.get(url, params=params, timeout=10)
    r.raise_for_status()
    d = r.json()
    return {
        "description": d["weather"][0]["description"],
        "temp_C": d["main"]["temp"],
        "feels_like_C": d["main"].get("feels_like"),
        "humidity": d["main"].get("humidity"),
        "wind_m_s": d.get("wind", {}).get("speed"),
        "raw": d
    }

def search_youtube(query, api_key, max_results=5):
    if not api_key:
        return []
    url = "https://www.googleapis.com/youtube/v3/search"
    params = {
        "part": "snippet",
        "q": query,
        "type": "video",
        "maxResults": max_results,
        "key": api_key
    }
    r = requests.get(url, params=params, timeout=10)
    r.raise_for_status()
    items = r.json().get("items", [])
    results = []
    for it in items:
        vid = it["id"]["videoId"]
        snip = it["snippet"]
        results.append({
            "title": snip["title"],
            "channel": snip["channelTitle"],
            "publishedAt": snip["publishedAt"],
            "url": f"https://www.youtube.com/watch?v={vid}"
        })
    return results

def search_hotels_google_places(place, api_key, max_results=5):
    if not api_key:
        return None
    url = "https://maps.googleapis.com/maps/api/place/textsearch/json"
    params = {"query": f"hotels in {place}", "key": api_key, "type": "lodging"}
    r = requests.get(url, params=params, timeout=10)
    r.raise_for_status()
    items = r.json().get("results", [])[:max_results]
    results = []
    for it in items:
        results.append({
            "name": it.get("name"),
            "address": it.get("formatted_address"),
            "rating": it.get("rating"),
            "user_ratings_total": it.get("user_ratings_total"),
            "place_id": it.get("place_id"),
            "map_url": f"https://www.google.com/maps/place/?q=place_id:{it.get('place_id')}"
        })
    return results

def run_app(host="127.0.0.1", port=5000):
    """Start the Flask-based web UI. Imports Flask at runtime so Flask is optional."""
    try:
        from flask import Flask, request, render_template_string, redirect, url_for
    except Exception:
        print("Flask not installed. Install with: pip install flask")
        return

    app = Flask(__name__)

    INDEX_HTML = """
    <!doctype html>
    <title>Place Explorer</title>
    <h2>Place Explorer</h2>
    <form method=post action="{{ url_for('search') }}">
      <label>Place: <input name=place required value="{{ place or '' }}"></label><br>
      <label>Max results: <input name=max type=number value="{{ max or 5 }}"></label><br>
      <label>Open videos: <input name=open_videos type=checkbox {{ 'checked' if open_videos else '' }}></label><br>
      <input type=submit value=Search>
    </form>
    """

    RESULT_HTML = """
    <!doctype html>
    <title>Results for {{ display_name or place }}</title>
    <a href="{{ url_for('index') }}">← Back</a>
    <h2>Results for: {{ display_name or place }}</h2>
    {% if error %}
      <p style="color:red">{{ error }}</p>
    {% endif %}
    {% if loc %}
      <p>Coordinates: {{ loc.lat }}, {{ loc.lon }}</p>
    {% endif %}

    {% if weather %}
      <h3>Weather</h3>
      <p>{{ weather.description.capitalize() }}, {{ weather.temp_C }} °C (feels like {{ weather.feels_like_C }} °C)</p>
    {% endif %}

    {% if videos %}
      <h3>YouTube videos</h3>
      <ul>
      {% for v in videos %}
        <li><a href="{{ v.url }}" target="_blank">{{ v.title }}</a> — {{ v.channel }}</li>
      {% endfor %}
      </ul>
    {% endif %}

    {% if hotels %}
      <h3>Hotels</h3>
      <ul>
      {% for h in hotels %}
        <li>{{ h.name }} — {{ h.address }} {% if h.rating %} ({{ h.rating }}){% endif %} <a href="{{ h.map_url }}" target="_blank">map</a></li>
      {% endfor %}
      </ul>
    {% endif %}
    """

    @app.route("/", methods=["GET"])
    def index():
        return render_template_string(INDEX_HTML, place="", max=5, open_videos=False)

    @app.route("/search", methods=["POST"])
    def search():
        place = request.form.get("place", "").strip()
        if not place:
            return redirect(url_for("index"))
        try:
            loc = geocode(place)
        except Exception as e:
            return render_template_string(RESULT_HTML, place=place, error=f"Geocoding failed: {e}", loc=None)
        if not loc:
            return render_template_string(RESULT_HTML, place=place, error="Place not found.", loc=None)

        display_name = loc.get("display_name")
        max_n = int(request.form.get("max") or 5)
        open_videos = bool(request.form.get("open_videos"))

        # Weather
        weather = None
        try:
            ow_key = os.getenv("OPENWEATHER_API_KEY")
            if ow_key:
                weather = get_weather(loc["lat"], loc["lon"], ow_key)
        except Exception:
            weather = None

        # YouTube
        vids = []
        try:
            yt_key = os.getenv("YOUTUBE_API_KEY")
            if yt_key:
                vids = search_youtube(place, yt_key, max_results=max_n)
        except Exception:
            vids = []

        # Hotels
        hotels = []
        try:
            gp_key = os.getenv("GOOGLE_PLACES_API_KEY")
            if gp_key:
                hotels = search_hotels_google_places(place, gp_key, max_results=max_n) or []
        except Exception:
            hotels = []

        return render_template_string(
            RESULT_HTML,
            display_name=display_name,
            place=place,
            loc=loc,
            weather=weather,
            videos=vids,
            hotels=hotels,
            error=None
        )

    print(f"Starting web UI at http://{host}:{port}/")
    app.run(host=host, port=port, debug=False)

def main():
    p = argparse.ArgumentParser(description="Show YouTube videos, weather, and hotels for a place.")
    p.add_argument("place", nargs="+", help="Place name (e.g. 'Paris, France')")
    p.add_argument("--open-videos", action="store_true", help="Open top YouTube videos in browser")
    p.add_argument("--max", type=int, default=5, help="Max items per category")
    args = p.parse_args()

    place = " ".join(args.place)
    print(f"Resolving place: {place} ...")
    loc = geocode(place)
    if not loc:
        print("Place not found.")
        sys.exit(1)
    print(f"Found: {loc['display_name']} (lat={loc['lat']}, lon={loc['lon']})\n")

    # Weather
    ow_key = os.getenv("OPENWEATHER_API_KEY")
    if ow_key:
        try:
            weather = get_weather(loc["lat"], loc["lon"], ow_key)
            print("Weather:")
            print(f"  {weather['description'].capitalize()}, {weather['temp_C']} °C (feels like {weather['feels_like_C']} °C)")
            print(f"  Humidity: {weather['humidity']}%  Wind: {weather['wind_m_s']} m/s\n")
        except Exception as e:
            print(f"Weather fetch failed: {e}\n")
    else:
        print("OPENWEATHER_API_KEY not set — skipping weather.\n")

    # YouTube videos
    yt_key = os.getenv("YOUTUBE_API_KEY")
    if yt_key:
        try:
            vids = search_youtube(place, yt_key, max_results=args.max)
            print("YouTube videos:")
            for i, v in enumerate(vids, 1):
                print(f"  {i}. {v['title']} — {v['channel']}")
                print(f"     {v['url']}")
            if args.open_videos and vids:
                for v in vids:
                    webbrowser.open(v["url"])
                    sleep(0.5)
            print()
        except Exception as e:
            print(f"YouTube search failed: {e}\n")
    else:
        print("YOUTUBE_API_KEY not set — skipping YouTube search.\n")

    # Hotels
    gp_key = os.getenv("GOOGLE_PLACES_API_KEY")
    if gp_key:
        try:
            hotels = search_hotels_google_places(place, gp_key, max_results=args.max)
            if hotels:
                print("Hotels (from Google Places):")
                for i, h in enumerate(hotels, 1):
                    print(f"  {i}. {h['name']} — {h['address']}")
                    if h.get("rating") is not None:
                        print(f"     Rating: {h['rating']} ({h.get('user_ratings_total', 0)} reviews)")
                    print(f"     Map: {h['map_url']}")
                print()
            else:
                print("No hotels found via Places.\n")
        except Exception as e:
            print(f"Google Places search failed: {e}\n")
    else:
        # Fallback: provide Google Maps search link
        q = urllib.parse.quote_plus(f"hotels in {place}")
        maps_url = f"https://www.google.com/maps/search/{q}"
        print("GOOGLE_PLACES_API_KEY not set — fallback to Google Maps search:")
        print(f"  {maps_url}\n")

if __name__ == "__main__":
    main()
    # Auto-start web UI if requested via env var
    if os.getenv("START_WEB_UI") == "1":
        try:
            run_app()
        except Exception as e:
            print(f"Web UI could not be started: {e}")