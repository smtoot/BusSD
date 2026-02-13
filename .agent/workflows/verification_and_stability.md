---
description: Mandatory Verification & Stability Protocol
---

# Verification & Stability Workflow

To ensure that no task is marked as "finished" while the application is broken, the following steps are MANDATORY before any `notify_user` call or final commit.

## 1. Syntax & Integrity Scan
- **PHP Syntax Check:** Run `php -l` on every modified file.
- **Migration Check:** Verify new migrations are valid and don't conflict with existing schemas.
- **Blade Consistency:** Ensure variables passed from controllers exactly match their usage in Blade templates.

## 2. Comprehensive Route Audit
- **Broken Link Sweep:** If any route name is changed (e.g., rebranding), perform a project-wide grep for the old route name strings (e.g., `route('old.name')`).
- **Route Registration:** Run `php artisan route:list` filtered by the changed name to confirm it is properly registered and points to the correct controller method.

## 3. Reference & Terminology Sweep
- **Variable Alignment:** Double-check that controller variables (e.g., `$totalApp`) are updated in every corresponding view.
- **JSON Configs:** Verify that sidebars, breadcrumbs, and settings JSON files reference the correct, active routes.

## 4. Cross-Panel Impact Analysis
- **Operator vs Admin:** If a change affects shared logic (like the `Crud` trait), verify it doesn't break the Admin panel while fixing the Operator panel.
- **Session/Guard Check:** Ensure `authUser()` usage is consistent and doesn't lead to guard-mismatch errors.

## 5. Regression Testing
- **Dashboard Load:** Always verify that the main dashboard and the most relevant list pages load without 500 errors after changes.
- **Form Submission:** If logic was touched, mock the validation or check the `store/update` logic for missing fields.

---
**Protocol:** If even one reference is found during the "Double-Check" sweep, the task is NOT finished. I will return to EXECUTION until a clean sweep is achieved.
