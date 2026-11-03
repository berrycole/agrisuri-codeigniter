# Guided proposal interface

Purpose: help classmates and AgriCoop staff understand one complete rice-order decision before using operational forms.

References: the existing AgriSuri operations dashboard and supplied agricultural palette. The previous dashboard's dense navigation and multiple simultaneous forms are reduced to one primary task per step.

Design contract:

- Start screen offers two clear paths: a six-scene proposal story or a five-step working simulation.
- Story scenes explain the recorded evidence, responsible person, and next decision without modifying inventory.
- Working steps explain the quantity meaning, required input, stock rule, and next action.
- Secondary transactions use disclosure panels; the current task stays visible.
- Keep the forest, olive, sky, cream, and lime palette. Use text labels along with status colors.
- Keyboard focus, visible form labels, success/error feedback, pending saves, and narrow-screen layouts are required.
- Preserve expected-versus-actual separation, held-stock restrictions, reservation balances, linked approvals, and partial buyer outcomes.
- V2 has its own browser storage key and CodeIgniter JSON file. V1 remains available.

Applied skill: Frontend UI Engineering from `addyosmani/agent-skills`, selected from the KDnuggets article supplied by the user. No agent orchestration or external AI service is added to the product.
