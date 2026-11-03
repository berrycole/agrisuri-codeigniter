# AgriSuri simulation

A CodeIgniter 4 pilot walkthrough based on the AgriSuri brainstorming PDF. It uses sample milled-rice records and stores changes in `writable/agrisuri.json`.

**Shareable preview:** https://berrycole.github.io/agrisuri-codeigniter/ — runs in each visitor's browser and keeps their sample changes locally. The CodeIgniter application is the PHP source in this repository.

## Run locally

```powershell
cd C:\Users\Ycole\Documents\ChatGPT\AgriCOOPh\agrisuri
& C:\php\php.exe spark serve
```

Open `http://localhost:8080`. If PHP and Composer are installed elsewhere, use their equivalent paths. Dependencies are already in `vendor/`; to reinstall, run `php composer.phar install --no-dev` from this folder.

## Demo path

1. Review the sample harvest plan and actual batch under **Supply & batches**.
2. Record inspection and package 200 kg of cleared stock.
3. Inspect the buyer-ready match and reserve up to 600 kg under **Buyer matching**.
4. Review descriptive figures, the labeled packaging scenario, and action choices.
5. Approve an intervention, assign its owner and deadline, then record its outcome.
6. Enter dispatch and buyer acceptance under **Fulfillment**.

Use **System scope → Reset sample records** to start again.

## Simulation boundaries

This is a functional workflow prototype, not a production deployment. It uses local JSON storage and a single staff view. The System scope page identifies planned login and permissions, audited corrections, attachments, reminders, broader value-chain records, and reviewed AI assistance. All quantities, costs, and buyer details are sample data. The scenario estimate assumes 100 kg of packaging per day; it is not a trained forecast.

## Public preview

The included `Dockerfile` and `render.yaml` prepare this PHP app for a Render web service. On Render's free plan, the service sleeps after inactivity and its local JSON changes can disappear on restart. The sample case is recreated when it wakes; this is suitable for a shared demonstration, not for real operational records.


## Updated interface and workflow

The CodeIgniter app and public browser simulation share `public/assets/app.js`, `dashboard.js`, and the CSS files. After changing shared assets, run `python tools/sync-preview.py` to refresh `docs/assets` for GitHub Pages. The PHP controller validates and persists server transactions; the public preview uses each visitor's local storage.

The interface includes the proposal guide, pilot walkthrough, supply chart, harvest calendar, buyer matching, explicit scenario assumptions, intervention cost comparison, assigned follow-ups, handling cost events, batch allocations, searchable records, CSV export, and print layout. The English/Taglish draft helper is a rule-based simulation and does not call an AI service. Real authentication, verified corrections and attachments, full stock adjustments, external notifications, trained forecasting, and optional satellite evidence remain planned.

The visual palette follows the supplied reference: `#ACAD2B`, `#BFE3FC`, `#F0F9FF`, `#28361F`, and `#596115`. Dashboard organization was informed by [Mosaic Lite](https://github.com/cruip/tailwind-dashboard-template) and the [analytics dashboard topic](https://github.com/topics/analytics-dashboard); this project keeps its CodeIgniter runtime and does not import the React template.

Check the browser simulation with `node tools/test-simulation.cjs`. PHP workflow checks additionally exercised invalid reservations, inspection balance, packaging, allocation, buyer feedback, handling, and intervention outcomes against the running local server.


## Version 2: guided proposal

Open https://berrycole.github.io/agrisuri-codeigniter/friendly/ for the second version. The original operations dashboard remains at the main URL.

The second version has a six-scene proposal story and a five-step working simulation with plain-language explanations. Its records are separate from the original version. Locally, use http://localhost:8080/friendly/ to run it through CodeIgniter. It shares the same stock rules, PHP transaction controller, and browser simulation engine. AI integration and full account permissions remain planned.

Frontend UI Engineering from [Addy Osmani's agent skills](https://github.com/addyosmani/agent-skills/tree/main/skills/frontend-ui-engineering) informed the layout, focus states, responsive checks, and error feedback. See `notes/friendly-design.md` for the design contract.
