# AgriSuri simulation

A CodeIgniter 4 pilot walkthrough based on the AgriSuri brainstorming PDF. It uses sample milled-rice records and stores changes in `writable/agrisuri.json`.

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
