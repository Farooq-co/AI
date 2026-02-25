import pytest
from types import SimpleNamespace

import main_1


class DummyResponse:
    def __init__(self, json_data, status_code=200):
        self._json = json_data
        self.status_code = status_code

    def raise_for_status(self):
        if self.status_code >= 400:
            raise Exception("HTTP error")

    def json(self):
        return self._json


def test_geocode_success(monkeypatch):
    def fake_get(url, params=None, headers=None, timeout=None):
        return DummyResponse([{"lat": "48.8566", "lon": "2.3522", "display_name": "Paris, France"}])

    monkeypatch.setattr(main_1, 'requests', SimpleNamespace(get=fake_get))

    res = main_1.geocode("Paris, France")
    assert res is not None
    assert abs(res['lat'] - 48.8566) < 1e-6
    assert abs(res['lon'] - 2.3522) < 1e-6
    assert 'Paris' in res['display_name']


def test_geocode_not_found(monkeypatch):
    def fake_get(url, params=None, headers=None, timeout=None):
        return DummyResponse([])

    monkeypatch.setattr(main_1, 'requests', SimpleNamespace(get=fake_get))

    res = main_1.geocode("NowherePlaceDoesNotExist")
    assert res is None


def test_geocode_403(monkeypatch):
    class Resp(DummyResponse):
        def raise_for_status(self):
            import requests as _requests
            # raise HTTPError with response attached
            raise _requests.HTTPError(response=self)

    def fake_get(url, params=None, headers=None, timeout=None):
        return Resp([], status_code=403)

    monkeypatch.setattr(main_1, 'requests', SimpleNamespace(get=fake_get))

    with pytest.raises(RuntimeError) as ei:
        main_1.geocode("lahore")
    assert 'Geocoding blocked (403)' in str(ei.value)


def test_geocode_uses_env(monkeypatch):
    captured = {}

    def fake_get(url, params=None, headers=None, timeout=None):
        captured['params'] = params
        captured['headers'] = headers
        return DummyResponse([{"lat": "31.5497", "lon": "74.3436", "display_name": "Lahore, Pakistan"}])

    monkeypatch.setenv('NOMINATIM_EMAIL', 'test@example.com')
    monkeypatch.setenv('NOMINATIM_USER_AGENT', 'MyAgent/1.0 (test@example.com)')
    monkeypatch.setattr(main_1, 'requests', SimpleNamespace(get=fake_get))

    res = main_1.geocode("lahore")
    assert res is not None
    assert captured['params']['email'] == 'test@example.com'
    assert captured['headers']['User-Agent'] == 'MyAgent/1.0 (test@example.com)'

