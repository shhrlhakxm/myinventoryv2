# MyInventory CI/CD Guide

## Purpose

This guide defines a small, production-minded CI/CD path for MyInventory. It is intentionally staged so that automated checks are established before any workflow receives production credentials or permission to deploy.

## CI/CD in plain language

**Continuous Integration (CI)** automatically verifies every proposed code change in a clean environment. For this project, CI should install the locked PHP and JavaScript dependencies, run the PHPUnit suite, and confirm that Vite can build the frontend.

**Continuous Delivery (CD)** keeps a change ready to release after CI passes, but a person approves the production deployment. **Continuous Deployment** goes one step further and deploys every passing change automatically.

This project should begin with continuous delivery and a manual production approval. Fully automatic production deployment can be considered later, after the deployment and rollback process is proven reliable.

## How it works in production

```text
Developer branch
      |
      v
Pull request -> CI tests and builds -> review -> merge to main
                                               |
                                               v
                                      approved release
                                               |
                                               v
                                  deploy -> health check
                                               |
                                  success <----+----> rollback
```

The CI runner starts clean for each workflow run, so passing locally is not enough. A merge is allowed only after the automated checks pass. The CD job then deploys the exact approved revision, uses secrets supplied by the deployment environment, applies safe production commands, and verifies the live application. A failed health check must stop the release and lead to rollback or manual recovery.

## Planned GitHub Actions workflows

### Stage 1: Continuous Integration

Create `.github/workflows/ci.yml` and run it for pull requests and pushes to `main`.

The first version should:

1. Check out the repository.
2. Configure PHP 8.4 and the extensions required by the application, including SQLite support.
3. Install PHP dependencies from `composer.lock` with `composer install`.
4. Copy `.env.example` to `.env` and generate a temporary application key for the runner.
5. Configure Node.js and install JavaScript dependencies from `package-lock.json` with `npm ci`.
6. Run `composer test --compact`.
7. Run `npm run build`.

The test configuration already uses an isolated in-memory SQLite database. CI must retain that isolation and must never connect to a local, shared, staging, or production database.

### Stage 2: Repository protection

After CI has passed on GitHub:

1. Require the CI check before merging into `main`.
2. Prefer changes through pull requests rather than direct pushes to `main`.
3. Keep workflow permissions read-only unless a job has a documented need for more access.

Branch-protection availability depends on the repository visibility and GitHub plan. If it is unavailable, treat a passing CI run as a manual merge rule.

### Stage 3: Choose the deployment target

Do not create the deployment workflow until the host is selected. The commands, authentication, artifact transfer, filesystem layout, queue handling, SSL, database access, and rollback method differ between:

- Laravel Cloud;
- Laravel Forge or a managed VPS;
- a manually managed VPS;
- shared hosting.

Record the chosen host, production URL, PHP/runtime services, deploy method, and rollback method before implementing CD.

#### Selected free portfolio target

The selected target for this noncommercial portfolio demonstration is:

- **Application hosting:** Render Free Web Service using a Docker image with PHP 8.4 and Apache.
- **Database hosting:** Aiven for MySQL Free Tier using a TLS-encrypted external connection.
- **Production URL:** Pending the first Render service creation and verified deployment.
- **Source authentication:** Render's GitHub App receives access to the repository. No GitHub or hosting token is committed.
- **Database authentication:** A dedicated Aiven service user is supplied through Render environment variables. The Aiven CA certificate is supplied as a Render secret file.
- **TLS secret-file access:** Render mounts Docker secret files for group ID `1000`. The container adds Apache's `www-data` user to that group so PDO can read `/etc/secrets/ca.pem`.
- **Initial deployment method:** Manual deployment from the Render dashboard with automatic deployment disabled.
- **Application rollback:** Use one of Render Free's two most recent deploys. Database migrations remain forward-only and require separate recovery planning.
- **Database recovery:** Use Aiven's managed free-tier backups and retain a separate logical export before risky schema changes.

Alwaysdata Free Public Cloud was evaluated first but rejected because its current registration flow required credit/debit-card validation. No payment details were provided.

This target is suitable only for a portfolio demonstration:

- Render Free services spin down after 15 minutes without inbound traffic and can take about one minute to restart.
- The Render filesystem is ephemeral, so it must not store persistent application data or user uploads.
- Render Free does not provide SSH, background workers, persistent disks, or pre-deploy commands.
- Database migrations therefore run through the container startup script using `php artisan migrate --force`.
- Queue work uses the synchronous driver because a separate free background worker is unavailable.
- Aiven Free provides a single-node MySQL service with 1 GB RAM and 1 GB storage. It may power down after prolonged inactivity.
- If a free allowance is exhausted and no payment method exists, the service must suspend rather than incur charges.
- Hosting credentials, database credentials, application keys, and TLS certificate contents must remain in the hosting dashboards and must never be committed.

### Stage 4: Continuous Delivery

The first CD workflow should deploy only a commit from `main` that has passed CI. Use a protected GitHub `production` environment and require manual approval when that feature is available.

A production release will normally include:

1. Retrieve or build the exact approved revision.
2. Install production dependencies from lock files.
3. Provide environment configuration through the host or GitHub secrets.
4. Put the application into maintenance mode only when the release requires it.
5. Run `php artisan migrate --force` for pending forward-only migrations.
6. Run `php artisan optimize`.
7. Restart long-running queue workers when the selected host uses them.
8. Restore traffic if maintenance mode was used.
9. Verify the application health endpoint and one important user journey.
10. Record the deployed revision and retain a documented rollback path.

The exact order will be finalized for the selected host. Database migrations require special care because reverting application code does not automatically reverse a database change.

## Safety rules

- Never commit `.env`, passwords, private keys, API tokens, or production database credentials.
- Store deployment secrets in the hosting platform or a protected GitHub environment, not in workflow YAML.
- Never run `migrate:fresh`, `migrate:refresh`, destructive seeders, or test commands against production data.
- Install dependencies from `composer.lock` and `package-lock.json`; do not update dependencies during a deployment.
- Do not deploy if CI fails or is skipped.
- Set production `APP_ENV=production` and `APP_DEBUG=false`.
- Grant workflow tokens and deployment credentials only the permissions they need.
- Use trusted actions and pin their versions; review automated action-version updates before merging.
- Prevent overlapping production deployments with a concurrency rule.
- Keep a database backup policy and a rollback procedure before enabling production CD.
- Treat logs and build artifacts as potentially visible; do not print secret values.

## Definition of done

### CI is complete when

- the workflow runs on pull requests and pushes to `main`;
- PHPUnit passes using the isolated test database;
- the production frontend bundle builds successfully;
- a failed test or build produces a failed GitHub check; and
- the workflow contains no production credentials.

### CD is complete when

- the deployment target and authentication method are documented;
- only a CI-approved revision can deploy;
- production secrets are environment-scoped;
- the deployment has an approval gate initially;
- migrations, optimization, worker restarts, and health checks are handled safely; and
- a tested rollback or recovery procedure is documented.

## Next action

Complete and merge the Render container configuration through the protected pull-request workflow. Then create the Render Free Web Service with automatic deployment disabled, configure production environment variables and the Aiven CA certificate through the Render dashboard, and perform the first manual deployment. Do not create a CD workflow until the live URL, startup migration, health check, and rollback behavior have been verified.

## Official references

- [GitHub Actions workflow triggers](https://docs.github.com/en/actions/how-tos/write-workflows/choose-when-workflows-run)
- [GitHub deployment environments and protection rules](https://docs.github.com/en/actions/reference/workflows-and-actions/deployments-and-environments)
- [GitHub Actions secure-use reference](https://docs.github.com/en/actions/reference/security/secure-use)
- [Laravel deployment documentation](https://laravel.com/docs/13.x/deployment)
- [Render Docker deployment](https://render.com/docs/docker)
- [Render Free service limits](https://render.com/docs/free)
- [Render environment variables and secret files](https://render.com/docs/configure-environment-variables)
- [Aiven for MySQL Free Tier](https://aiven.io/docs/products/mysql/concepts/mysql-free-tier)
- [Render Docker secret files](https://render.com/docs/docker-secrets)
