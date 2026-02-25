import click
from typing import Optional

# Import functions from main_1
from main_1 import geocode, get_weather, search_youtube, search_hotels_google_places, USER_AGENT


@click.group()
def cli():
    """AI & Data Science CLI"""
    pass


@cli.command()
@click.argument("place", nargs=-1, required=True)
@click.option("--max", "max_n", default=5, help="Max items per category")
@click.option("--open-videos", is_flag=True, default=False, help="Open top YouTube videos in browser")
def search(place, max_n, open_videos):
    """Search for a place and print videos, weather and hotels"""
    place_str = " ".join(place)
    click.echo(f"Resolving place: {place_str} ...")
    loc = geocode(place_str)
    if not loc:
        click.echo("Place not found.")
        raise SystemExit(1)
    click.echo(f"Found: {loc['display_name']} (lat={loc['lat']}, lon={loc['lon']})\n")

    ow_key = None
    try:
        import os
        ow_key = os.getenv("OPENWEATHER_API_KEY")
    except Exception:
        ow_key = None

    if ow_key:
        try:
            weather = get_weather(loc["lat"], loc["lon"], ow_key)
            if weather:
                click.echo("Weather:")
                click.echo(f"  {weather['description'].capitalize()}, {weather['temp_C']} °C (feels like {weather['feels_like_C']} °C)")
                click.echo(f"  Humidity: {weather['humidity']}%  Wind: {weather['wind_m_s']} m/s\n")
        except Exception as e:
            click.echo(f"Weather fetch failed: {e}\n")
    else:
        click.echo("OPENWEATHER_API_KEY not set — skipping weather.\n")

    yt_key = None
    try:
        import os
        yt_key = os.getenv("YOUTUBE_API_KEY")
    except Exception:
        yt_key = None

    if yt_key:
        try:
            vids = search_youtube(place_str, yt_key, max_results=max_n)
            click.echo("YouTube videos:")
            for i, v in enumerate(vids, 1):
                click.echo(f"  {i}. {v['title']} — {v['channel']}")
                click.echo(f"     {v['url']}")
            if open_videos and vids:
                import webbrowser
                from time import sleep
                for v in vids:
                    webbrowser.open(v['url'])
                    sleep(0.5)
            click.echo()
        except Exception as e:
            click.echo(f"YouTube search failed: {e}\n")
    else:
        click.echo("YOUTUBE_API_KEY not set — skipping YouTube search.\n")

    gp_key = None
    try:
        import os
        gp_key = os.getenv("GOOGLE_PLACES_API_KEY")
    except Exception:
        gp_key = None

    if gp_key:
        try:
            hotels = search_hotels_google_places(place_str, gp_key, max_results=max_n)
            if hotels:
                click.echo("Hotels (from Google Places):")
                for i, h in enumerate(hotels, 1):
                    click.echo(f"  {i}. {h['name']} — {h['address']}")
                    if h.get("rating") is not None:
                        click.echo(f"     Rating: {h['rating']} ({h.get('user_ratings_total', 0)} reviews)")
                    click.echo(f"     Map: {h['map_url']}")
                click.echo()
            else:
                click.echo("No hotels found via Places.\n")
        except Exception as e:
            click.echo(f"Google Places search failed: {e}\n")
    else:
        # Fallback: provide Google Maps search link
        import urllib.parse
        q = urllib.parse.quote_plus(f"hotels in {place_str}")
        maps_url = f"https://www.google.com/maps/search/{q}"
        click.echo("GOOGLE_PLACES_API_KEY not set — fallback to Google Maps search:")
        click.echo(f"  {maps_url}\n")


@cli.command()
@click.option("--host", default="127.0.0.1", help="Host to bind to")
@click.option("--port", default=5000, help="Port to bind to")
def serve(host: str, port: int):
    """Start the web UI"""
    # Import run_app from main_1 and start
    from main_1 import run_app
    run_app(host=host, port=port)
