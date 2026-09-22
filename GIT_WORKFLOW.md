# MyInventory Git Workflow

## Purpose

This workflow keeps `main` stable, makes every change reviewable, and prevents work from being lost or omitted from a pull request (PR). Use it for features, fixes, documentation, deployment changes, and refactoring.

The normal path is:

```text
Update main -> create a branch -> make and verify changes -> commit -> push
     -> open a PR -> pass CI and review -> squash and merge -> clean up
```

## Core ideas

| Term | Description and purpose |
| --- | --- |
| `main` | The protected, stable branch. Do not develop or commit directly on it. |
| Working branch | A short-lived branch containing one focused change. It isolates unfinished work from `main`. |
| Commit | A named snapshot of staged changes. Uncommitted files cannot appear in a PR. |
| Push | Uploads local commits to GitHub. A locally committed change is not in a PR until it is pushed to the PR's source branch. |
| Pull request | A request to merge one source branch into a base branch, normally `main`. While the PR is open, newly pushed commits on its source branch appear automatically. |
| CI | GitHub's `Tests and build` check. It installs dependencies, builds frontend assets, and runs PHPUnit in a clean environment. |
| Squash merge | Combines all PR commits into one commit on `main`, keeping project history concise. |

## Complete workflow

Replace example branch and file names with names that describe the current task.

### 1. Check the current state

```bash
git status -sb
git branch --show-current
```

**Purpose:** Confirm the current branch and detect uncommitted work before changing branches. Finish, commit, or intentionally stash existing work before starting an unrelated task. Do not mix unrelated changes in one branch or PR.

### 2. Update local `main`

```bash
git switch main
git pull --ff-only origin main
```

**Purpose:** Start from the latest accepted project state. `--ff-only` stops Git instead of creating an accidental merge commit when local `main` has diverged.

### 3. Create a focused branch

```bash
git switch -c fix/short-description
```

Use a clear category:

| Prefix | Use for | Example |
| --- | --- | --- |
| `feature/` | New user-facing behavior | `feature/low-stock-alerts` |
| `fix/` | Bug corrections | `fix/update-user-seeder` |
| `docs/` | Documentation only | `docs/git-workflow-guide` |
| `refactor/` | Internal restructuring without behavior changes | `refactor/item-service` |
| `chore/` | Maintenance or tooling | `chore/update-ci-cache` |

**Purpose:** A branch gives the change its own reviewable history and keeps incomplete work away from `main`.

### 4. Make and review the change

After editing, inspect exactly what changed:

```bash
git status -sb
git diff
git diff --check
```

**Purpose:** `git diff` catches accidental edits before they are committed. `git diff --check` detects whitespace errors.

### 5. Run relevant local checks

For PHP changes:

```bash
vendor/bin/pint --dirty --format agent
composer test --compact
```

For frontend code or asset changes:

```bash
npm run build
```

Pure documentation changes normally do not need application tests locally. GitHub CI still runs the full `Tests and build` check on the PR.

**Purpose:** Local checks provide fast feedback. CI repeats the important checks in a clean environment before merge.

### 6. Stage only the intended files

```bash
git add path/to/changed-file
git diff --staged
```

Repeat `git add` for each intended file. Prefer explicit paths over `git add .` when unrelated edits exist.

**Purpose:** Staging defines exactly what the next commit will contain. Reviewing `--staged` prevents unrelated files, debug output, or secrets from entering the commit.

### 7. Commit the change

```bash
git commit -m "fix: update user seeder credentials"
```

Use a short, action-oriented message:

```text
<type>: <what changed>
```

Common types are `feat`, `fix`, `docs`, `test`, `refactor`, and `chore`.

**Purpose:** A good commit message explains the change in history. The commit records staged files only; unstaged and untracked files remain local.

### 8. Push the branch

For the first push:

```bash
git push -u origin fix/short-description
```

For later pushes on the same branch:

```bash
git push
```

**Purpose:** Pushing uploads commits to GitHub. `-u` connects the local branch to its remote branch so later `git push` and `git pull` commands know which branch to use.

### 9. Open the pull request

On GitHub, create a PR with:

- **Base branch:** `main`
- **Compare/source branch:** the working branch, such as `fix/short-description`
- **Title:** a concise summary of the result
- **Description:** what changed, why it was needed, and how it was verified

Suggested description:

```markdown
## What

- Describe the change.

## Why

- Explain the problem or goal.

## Verification

- List tests, builds, or manual checks performed.
```

**Purpose:** The PR is the review and CI boundary. Checking its base and source branches prevents changes from being compared against the wrong branch.

### 10. Address feedback on the same branch

```bash
# Make the requested edits and rerun relevant checks.
git add path/to/changed-file
git commit -m "fix: address review feedback"
git push
```

**Purpose:** An open PR follows its source branch. Pushing another commit to that same branch updates the existing PR; a second PR is not needed.

Before merging, confirm that:

- the PR's **Commits** and **Files changed** tabs contain the expected work;
- the `Tests and build` check passes;
- review comments are resolved; and
- no secrets or unrelated changes are included.

### 11. Squash and merge

Choose **Squash and merge** on GitHub after CI and review pass. Check the final commit title, then confirm the merge.

**Purpose:** Squashing turns the PR into one logical commit on `main`. Small work-in-progress and review-fix commits do not clutter the permanent history.

The squash commit receives a new commit ID. Therefore, the original branch commit IDs will not appear in the history of `main`, even though their combined changes are present.

### 12. Clean up after the merge

First ensure there is no unfinished work on the merged branch:

```bash
git status -sb
```

Then update local `main`:

```bash
git switch main
git pull --ff-only origin main
git fetch --prune
```

Delete the branch on GitHub using **Delete branch**. Try the safe local deletion:

```bash
git branch -d fix/short-description
```

After a squash merge, Git may report that the branch is not fully merged because the squash commit has a different ID. Only after confirming that the PR was merged and all wanted changes are on `main`, remove the local branch with:

```bash
git branch -D fix/short-description
```

If GitHub did not remove the remote branch, delete it only after the same confirmation:

```bash
git push origin --delete fix/short-description
```

**Purpose:** Cleanup prevents stale branches from being reused accidentally. `-D` is a forced local deletion, so never use it when the branch contains unmerged or uncommitted work that must be kept.

## If a commit is missing from a PR

A PR does not expire. Work through these checks in order:

```bash
git status -sb
git branch --show-current
git branch -vv
git log --oneline --decorate -5
```

| Finding | Meaning | Action |
| --- | --- | --- |
| Files appear under `Changes not staged`, `Changes to be committed`, or as untracked | They are not committed and cannot be in the PR. | Review, stage, commit, and push them. |
| The current branch has no upstream branch in `git branch -vv` | The branch has not had its first push. | Run `git push -u origin <branch-name>`. |
| Status shows `[ahead N]` | Local commits have not been pushed. | Run `git push`. |
| The commit is pushed, but the PR uses another source branch | The PR watches only the branch selected when it was opened. | Push to the correct branch or open a PR from the intended branch. |
| The PR is still open and uses the correct source branch | Pushed commits should appear automatically. | Refresh GitHub and check the PR's **Commits** tab. |
| The PR was already merged or closed | Later commits do not join that old PR. | Update `main`, create a new focused branch if needed, and open a new PR. |

Never add new work to a branch after its PR has been merged. Start the next change from the updated `main` branch.

## Quick checklist

```text
[ ] git status -sb
[ ] git switch main
[ ] git pull --ff-only origin main
[ ] git switch -c <type>/<short-description>
[ ] Make one focused change
[ ] Review the diff and run relevant checks
[ ] Stage explicit files and review the staged diff
[ ] Commit with a clear message
[ ] Push the branch
[ ] Open PR: working branch -> main
[ ] Confirm expected commits/files and passing Tests and build
[ ] Squash and merge
[ ] Update main and delete the merged branch
```

## Safety reminders

- Do not commit directly to `main`.
- Do not mix unrelated work in one branch or PR.
- Do not commit `.env`, credentials, tokens, private keys, or production data.
- Do not force-push `main`.
- Do not merge while CI is failing or expected files are missing.
- Do not delete a branch until its PR is merged and its wanted changes are confirmed on `main`.
