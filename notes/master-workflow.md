# AgriSuri connected workflow — version 3

Inputs: the two updated Shared Content PDFs, the master workflow diagrams, the environmental verification branch, and the compact bottleneck loop supplied by the user. The latest PDFs have the same seven-stage requirements. Documents are reference material; only synthetic data is published.

Design contract:

- Keep the supplied nine-color meanings and accompany every color with a readable action-type label.
- Expose a navigable workflow map, not a single unmanageable diagram. Registration and case decisions support the seven value-chain stages.
- Preserve the source stage numbers; market access can run before logistics in the actual decision sequence.
- Connect verified evidence to descriptive findings, an assumption-based readiness scenario, intervention comparison, manager approval, execution and outcome evaluation.
- Show correction, more-evidence, still-not-ready and partial/rejected branches explicitly.
- The optional environment branch must never directly change actual stock or declare crop disease, failure or exact yield.
- Preserve original receipts, distinguish processing output/by-product/loss, and account for dispatch separately from buyer rejection.

Implementation:

- `workflow-engine.js` is a pure transaction state machine shared by the public preview and the CodeIgniter-served page. Failed operations leave prior state intact.
- `workflow-views.js` contains typed action panels, the map, forms and seven-stage reference information.
- `workflow-app.js` owns navigation, local persistence, accessible feedback and event export.
- The CodeIgniter route `/workflow/` serves this isolated browser simulation. Its verified records use the `agrisuri-master-workflow-v3` browser storage key; database integration, authenticated permissions and immutable correction audits are planned. Earlier versions keep their own storage.

Working pilot actions include onboarding decisions, product verification, supply drafts and correction requests, expected/actual verification, optional environmental scenarios and ground checks, actual receipt, inspection, selected same-form processing, packaging, storage cost/location, buyer verification, match decisions, evidence-supported cases, quotation scenarios, evidence tasks, approved packaging or date-change interventions, reservations, dispatch, actual delivery, verified acceptance, accepted-quantity sales and outcome closure/reopening.

Explicit limits: one cooperative, one batch, one buyer request and one delivery in a demonstration. Multi-batch aggregation and form conversion require linked inventory records and remain planned. Alternative-stock quantities in the comparison are hypothetical and cannot be reserved. AI preparation/explanation uses rules and templates; no external LLM, weather or imagery service is called. Attachments, full accounting, live reminders and trained forecasting remain planned.

Verification: `node tools/test-master-workflow.cjs` covers input correction, expected-stock exclusion, environmental verification without inventory mutation, processing balance, evidence requests, approval/execution, duplicate reservation protection, partial acceptance, history preservation and outcome reopening/closure.
