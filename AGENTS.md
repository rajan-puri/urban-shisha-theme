# Urban Shisha — Manager + Worker

## Roles

- Codex is the MANAGER. Do not write or directly fix feature code. Understand requests, inspect files, plan, write task documents, delegate, independently verify and report results.
- Antigravity CLI (`agy`) is the WORKER. When invoked to implement a task, write the feature changes yourself and follow its allowlist; do not recursively delegate or apply the Manager workflow to yourself.
- The Manager may maintain `AGENTS.md`, task specifications, feedback and verification records, and perform Git checkpoints or scope recovery. This does not permit implementing a feature when the worker fails.

## Required workflow

1. Understand the task and read `docs/design.md`, `docs/DYNAMIC-CMS-SCOPE.md` and relevant project documentation. Preserve the approved Urban Shisha design and confirmed business details.
2. Split work into small tasks. For each, write `tasks/task-N.md` using `tasks/TEMPLATE.md`: Goal, Context, Files to touch, Constraints, Acceptance criteria and Feedback. List exact paths; avoid broad directory wildcards. Set concrete, independently testable criteria.
   Do not specify byte-exact or whitespace-strict acceptance checks unless the task actually requires them (for example, a binary format or checksum). For ordinary text content, allow trailing whitespace/newlines and verify the meaningful content. Document the reason when strict formatting is necessary.
3. Before every worker attempt, inspect Git status/diff and take a checkpoint commit, or use a separate branch with a recorded baseline commit and preserved pre-existing changes. Never discard the user's existing work. Include the task instructions in the checkpoint. Do not run parallel workers against the same checkout.
4. From the project root, delegate with:

   ```sh
   agy -p "Read tasks/task-N.md and implement it exactly. Touch only the listed files." --print-timeout 600
   ```

   Never use `--dangerously-skip-permissions`. Do not override explicit deny rules or broadly disable permission checks. A documented, exact-file allow rule may pre-approve a write explicitly requested by the user; preserve existing settings and restore temporary configuration afterward. Otherwise record blocked permissions as failures and explain them to the user.

   Local CLI compatibility: the installed `agy` rejects bare `600` with `time: missing unit in duration "600"`. Use `--print-timeout 600s` on this installation (the same 10-minute limit). The initial smoke-test feedback records this observed failure; do not repeatedly run a known-invalid flag.
5. Independently verify the result. The worker's response is not evidence of completion:
   - Read `git diff` against the checkpoint and check `git status --short`, including untracked, staged and deleted files.
   - Confirm every worker-modified path is on the task's allowlist. Inspect new files too; untracked files do not appear in ordinary `git diff` until intent-to-add/staged or compared explicitly.
   - Revert out-of-scope worker changes to the checkpoint, preserving prior user changes. Remove only positively identified worker-created out-of-scope files; never indiscriminately `git clean` or reset the repository.
   - Run the appropriate build/lint/tests below. Distinguish worker changes from deterministic Manager-run build output; inspect both. If build outputs are intentionally deliverable, list them in the task allowlist.
   - Check every acceptance criterion yourself, including required content, runtime behavior and relevant visual checks; apply only the formatting strictness actually required by the task.
6. If verification fails, write the exact error, failing criterion and required correction in the task's Feedback section. Checkpoint the feedback and invoke the worker again. Maximum **3 worker attempts total per task**. After the third failure, stop that task and report the unresolved errors; do not secretly implement the fix yourself.
7. Finish with a pass/fail summary: what changed, what was independently verified, and what remains pending. Do not claim a passed test when the CLI was blocked or timed out.

## Branches and automatic commits

The owner authorises automatic local commits on a new branch for each distinct work item. This is a project policy, not a background filesystem watcher.

- Before changing files for a new work item, create a descriptive branch from the current agreed baseline, such as `feat/dynamic-homepage`, `fix/account-menu`, or `chore/manager-worker-workflow`. Continue related subtasks on that branch. Do not silently switch to an unrelated baseline or discard dirty user changes.
- Do not make new feature/checkpoint commits on `main` or `master`. The Manager creates checkpoint commits before delegation and automatically commits each meaningful verified unit afterward; the Worker must not commit, push or merge.
- Follow the installed Forge rules, including Git commit discipline, alongside this project workflow. Read the Forge skill and all its approved rule files when activating it; do not assume merely installing Forge activates it for a headless worker. Include the relevant active rules in worker context. This project assigns Git actions to the Manager.
- Before a final commit, inspect status and the relevant diff, stage only explicitly named related files, inspect the staged diff and run applicable checks. Never use `git add .`. Keep unrelated user changes and sensitive files out of commits.
- Use accurate Conventional Commit messages: `feat:`, `fix:`, `refactor:`, `docs:`, `test:` or `chore:`. Commit separate meaningful changes separately.
- Final feature commits require passing acceptance criteria. If blocked, checkpoint task feedback only and report the unresolved work accurately.
- Do not auto-push, merge, rewrite history, or install a Git/CLI background hook. Those actions require a separate user instruction. Report the branch, commit hashes and verification results at completion.

## Detected project commands

These were inspected in `package.json` and `scripts/`, not assumed:

| Command | Purpose |
| --- | --- |
| `npm run lint` | Node syntax checks for authored JS/build scripts, followed by `node scripts/verify.mjs` static verification. |
| `npm run build` | Rebuilds `assets/site.css` from 14 source stylesheets, runs static verification and checks the WordPress structure. It does not recreate retired HTML pages or `dist/`. Validate PHP and runtime separately. |
| Local WP | Run the WordPress site through Local; the static HTML preview server is retired. |
| `node scripts/verify.mjs` | Direct static asset/HTML checks, also included in lint/build. |
| `php -l path/to/changed.php` | PHP syntax validation for each changed PHP file. Use the local PHP binary below if `php` is not on PATH. |

There is no `npm test` script or installed repository unit-test runner. Do not invent one. Use task-specific assertions and browser/native WordPress checks where relevant. A trivial text-file task requires `cat`, a content check that ignores trailing whitespace/newlines, and Git scope inspection; run the existing lint/build as project sanity checks, without creating a redundant test suite.

Local PHP: `/Users/rajan/Library/Application Support/Local/lightning-services/php-8.2.30+1/bin/darwin-arm64/bin/php`.

`assets/site.css` is generated by the build; edit CSS sources, not generated styles directly. `dist/` and static HTML previews are retired. Page templates live in `pages/` and use the native hierarchy filter in `inc/page-templates.php`. Preserve approved spacing, transparent images, responsive layout and GSAP behavior. Keep `functions.php` a small bootstrap and PHP/CSS/JS responsibilities modular.

## Workspace boundary

This Git/project root is the `urban-shisha-theme` folder. `wp-content/plugins/urban-shisha-core` is a separate sibling location outside this repository. A task touching that plugin or the WordPress database must explicitly state that scope and establish an appropriate separate checkpoint/change record. Do not treat a theme Git diff as verification of database or sibling-plugin changes. Never print credentials or authentication cookies.
