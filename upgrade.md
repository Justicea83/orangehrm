# Upgrade Guide: Merging Upstream Versions Incrementally

## Overview
This guide explains the step-by-step process to incrementally merge upstream changes from an open-source repository into your fork, ensuring a smooth upgrade while handling conflicts effectively.

## Prerequisites
- Your forked repository is set up with the upstream repository.
- Your fork has multiple commits ahead but is behind in upstream updates.
- You want to merge updates incrementally, starting from an earlier version.

## Step 1: Verify Remotes
Before beginning, ensure that your fork and the upstream repository are correctly configured:

```sh
git remote -v
```
Expected output:
```
origin    https://github.com/Justicea83/orangehrm.git (fetch)
origin    https://github.com/Justicea83/orangehrm.git (push)
upstream  https://github.com/orangehrm/orangehrm.git (fetch)
upstream  https://github.com/orangehrm/orangehrm.git (push)
```

If `upstream` is missing, add it:
```sh
git remote add upstream https://github.com/orangehrm/orangehrm.git
```

## Step 2: Fetch the Latest Upstream Changes
Before merging, fetch the latest updates from the upstream repository:

```sh
git fetch upstream
```
This ensures you have the latest updates available locally.

## Step 3: Check Out Your Main Working Branch
Ensure you are on your main upgrade branch before merging:

```sh
git checkout orangehrm-main
```
Pull the latest changes from your fork:

```sh
git pull origin orangehrm-main
```

## Step 4: Backup Before Merging
Before merging, create a backup branch in case anything goes wrong:

```sh
git checkout -b backup-before-merge
```

## Step 5: Merge Upstream Changes Incrementally
Start merging upstream versions step by step. If your target is `5.7`, but you're currently on `5.4`, merge `5.5` first.

### Merge `upstream/5.6`
```sh
git checkout orangehrm-main  # Ensure you're on the correct branch
git merge upstream/5.5
```
- If there are **no conflicts**, commit and push:
  ```sh
  git push origin orangehrm-main
  ```
- If there are **conflicts**, resolve them manually, then:
  ```sh
  git add .
  git commit -m "Merged upstream 5.5 into orangehrm-main"
  git push origin orangehrm-main
  ```

### Test Your Code
After merging each version, ensure that your application works correctly before proceeding to the next version.

### Merge `upstream/5.6`
```sh
git merge upstream/5.6
```
Resolve conflicts if any, commit, and push.

Repeat this process for:
- `upstream/5.6.1`
- `upstream/5.7`
- `upstream/5.x-experimental`
- `upstream/main` (Final Merge)

## --->> TaskflowHR API Upgrades
Add Migration upgrades to taskflowh-hr-api version to migration the langs and screens

## Step 6: Verify and Cleanup
After the final merge:
```sh
git status
```
Ensure your branch is up to date:
```sh
git log origin/orangehrm-main..upstream/main --oneline
```
If necessary, push your final changes:
```sh
git push origin orangehrm-main
```

## Best Practices
1. **Backup before merging**: Always create a backup branch before major merges.
2. **Test after each merge**: Run unit tests and verify functionality before proceeding.
3. **Commit incrementally**: Push changes after each successful merge to avoid conflicts later.

By following this structured approach, you can successfully upgrade your fork without major disruptions. 🚀

