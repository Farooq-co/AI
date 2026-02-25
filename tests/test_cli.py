from click.testing import CliRunner
from types import SimpleNamespace

import main_1
from ai_and_data_science.cli import cli


def test_search_command_success(monkeypatch):
    # Mock geocode
    def fake_geocode(place):
        return {"lat": 48.8566, "lon": 2.3522, "display_name": "Paris, France"}

    # Mock weather
    def fake_get_weather(lat, lon, api_key):
        return {"description": "clear sky", "temp_C": 20, "feels_like_C": 19, "humidity": 50, "wind_m_s": 3}

    # Mock youtube
    def fake_search_youtube(query, api_key, max_results=5):
        return [{"title": "Paris Tour", "channel": "Travel", "url": "https://youtube.example/1"}]

    # Mock hotels
    def fake_search_hotels(place, api_key, max_results=5):
        return [{"name": "Hotel Paris", "address": "1 Rue de Paris", "rating": 4.5, "map_url": "https://maps.example/1"}]

    # Note: cli.py imports these functions at module import time. Patch them on the cli module.
    import ai_and_data_science.cli as cli_mod
    monkeypatch.setattr(cli_mod, 'geocode', fake_geocode)
    monkeypatch.setattr(cli_mod, 'get_weather', fake_get_weather)
    monkeypatch.setattr(cli_mod, 'search_youtube', fake_search_youtube)
    monkeypatch.setattr(cli_mod, 'search_hotels_google_places', fake_search_hotels)

    # Also ensure main_1 functions are patched in case code calls them directly
    monkeypatch.setattr(main_1, 'geocode', fake_geocode)
    monkeypatch.setattr(main_1, 'get_weather', fake_get_weather)
    monkeypatch.setattr(main_1, 'search_youtube', fake_search_youtube)
    monkeypatch.setattr(main_1, 'search_hotels_google_places', fake_search_hotels)

    # Set fake env vars so CLI attempts each section (functions are mocked above)
    monkeypatch.setenv('OPENWEATHER_API_KEY', 'fake')
    monkeypatch.setenv('YOUTUBE_API_KEY', 'fake')
    monkeypatch.setenv('GOOGLE_PLACES_API_KEY', 'fake')

    runner = CliRunner()
    result = runner.invoke(cli, ['search', 'Paris, France'])
    assert result.exit_code == 0
    out = result.output
    assert 'Resolving place' in out
    assert 'Found: Paris, France' in out
    assert 'Weather:' in out
    assert 'YouTube videos:' in out
    assert 'Hotels (from Google Places):' in out


def test_search_missing_arg():
    runner = CliRunner()
    result = runner.invoke(cli, ['search'])
    assert result.exit_code != 0
    assert 'Error' in result.output


def test_serve_invokes_run_app(monkeypatch):
    called = {}

    def fake_run_app(host='127.0.0.1', port=5000):
        called['host'] = host
        called['port'] = port

    monkeypatch.setattr(main_1, 'run_app', fake_run_app)

    runner = CliRunner()
    result = runner.invoke(cli, ['serve', '--host', '0.0.0.0', '--port', '8080'])
    assert result.exit_code == 0
    assert called.get('host') == '0.0.0.0'
    assert int(called.get('port')) == 8080
