# TICKET-02 — SQL dump removal (runbook notes)

## What was removed from the repo tree

Tracked files under `database/` that were **full exports or migration scripts containing real-schema/data patterns**, including `u820431346_smis.sql` (phpMyAdmin-style dump referencing host DB name).

## Recurrence prevention

- `.gitignore` includes `/database/*.sql`.
- Use **migrations + seeders** for schema/data in git; keep one-off exports **outside** the repo or in approved secret storage.

## Follow-up (not done in Sprint 1 scope)

| Item | Action |
|------|--------|
| **Git history** | Removing files does **not** purge past commits. If dumps contained production credentials or PII, schedule **history scrub** (`git filter-repo`/BFG) + force-push policy with team **or** treat leaked secrets as **rotated/compromised**. |
| **Secret rotation** | If `u820431346_smis.sql` or imports reflected **real** hosts/users/passwords, rotate those credentials and audit access logs for that window. |

## Validation command

```bash
git ls-files '*.sql'
```

Expect **no output** when clean.
