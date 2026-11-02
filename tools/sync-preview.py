from pathlib import Path
import shutil

root = Path(__file__).resolve().parents[1]
target = root / 'docs/assets'
target.mkdir(parents=True, exist_ok=True)
for asset in (root / 'public/assets').iterdir():
    if asset.is_file():
        shutil.copyfile(asset, target / asset.name)
