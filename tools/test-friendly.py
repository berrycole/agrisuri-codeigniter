"""Run against the local CodeIgniter server; restore sample files afterward."""
from pathlib import Path
import urllib.request
import urllib.parse
import urllib.error
import json
import re

primary = Path('writable/agrisuri.json')
friendly = Path('writable/agrisuri-friendly.json')
before = primary.read_bytes() if primary.exists() else None
saved = friendly.read_bytes() if friendly.exists() else None

def post(kind, **values):
    body = urllib.parse.urlencode(dict(type=kind, ajax='1', workspace='friendly', **values)).encode()
    try:
        with urllib.request.urlopen('http://127.0.0.1:8080/action', data=body) as response:
            return json.load(response)
    except urllib.error.HTTPError as error:
        return json.load(error)

try:
    post('reset')
    assert post('reserve', request='B-001', batch='R-001', qty=600)['error']
    assert not post('package', batch='R-001', qty=200, cost=1200)['error']
    assert not post('reserve', request='B-001', batch='R-001', qty=600)['error']
    html = urllib.request.urlopen('http://127.0.0.1:8080/friendly/').read().decode()
    data = json.loads(re.search(r'window.AGRISURI_BOOTSTRAP=(.*?);</script>', html).group(1))
    assert data['batches'][0]['packaged'] == 600
    assert data['requests'][0]['reserved'] == 600
    assert (primary.read_bytes() if primary.exists() else None) == before
    print('PASS: friendly transactions persist and remain separate from V1.')
finally:
    if saved is not None:
        friendly.write_bytes(saved)
    elif friendly.exists():
        friendly.unlink()
