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


def test_get_weather_no_key():
    assert main_1.get_weather(48.85, 2.35, None) is None


def test_get_weather_success(monkeypatch):
    payload = {
        "weather": [{"description": "clear sky"}],
        "main": {"temp": 20, "feels_like": 19, "humidity": 50},
        "wind": {"speed": 3}
    }

    def fake_get(url, params=None, timeout=None):
        return DummyResponse(payload)

    monkeypatch.setattr(main_1, 'requests', SimpleNamespace(get=fake_get))

    res = main_1.get_weather(48.85, 2.35, "fakekey")
    assert res is not None
    assert res['description'] == 'clear sky'
    assert res['temp_C'] == 20
    assert res['feels_like_C'] == 19
    assert res['humidity'] == 50
    assert res['wind_m_s'] == 3
