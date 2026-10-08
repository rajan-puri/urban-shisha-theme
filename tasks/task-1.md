# Task 1 — Manager/Worker smoke test

## Goal

Create `hello.txt` at the project root containing exactly `manager test ok`.

## Context

Project root: `/Users/rajan/Local Sites/urban-shisha/app/public/wp-content/themes/urban-shisha-theme`.
This is a delegation smoke test, not a website feature. AGENTS.md defines Codex as Manager and you (agy) as Worker. Implement the file directly; do not delegate again.

## Files to touch

- `hello.txt` — create this file only.

## Constraints

- File bytes must be exactly UTF-8 `manager test ok` (14 bytes), without a trailing newline, BOM, quotes or extra whitespace.
- Do not modify task documents, AGENTS.md, theme code, assets, plugin files or database data.
- Do not install anything, run builds that modify files, or commit changes. The Manager performs verification and Git operations.

## Acceptance criteria

- [ ] `hello.txt` exists as a regular file at the project root.
- [ ] `cat hello.txt` displays `manager test ok`.
- [ ] Exact-byte verification equals `b'manager test ok'` (14 bytes).
- [ ] Git inspection shows only `hello.txt` changed by the Worker.
- [ ] Manager-run `npm run lint` and `npm run build` pass; any generated changes are separately inspected.

## Feedback

No attempts yet. Manager will record actual CLI and verification results here.
