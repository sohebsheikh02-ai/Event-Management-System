# Deploy On Render Free Plan

This project is configured for:

1. Free Render web service
2. External free MySQL database
3. Docker-based PHP deployment

## What To Use

- Render Free Web Service for the app
- External MySQL database for storage

Examples of external MySQL providers:
- Railway MySQL
- PlanetScale
- FreeSQLDatabase
- Aiven trial MySQL

Use any provider that gives you these values:
- host
- port
- database name
- username
- password

## Exact Environment Variables

Set these in Render for your web service:

- `APP_BASE_URL=https://your-app-name.onrender.com`
- `DB_HOST=your-mysql-host`
- `DB_PORT=3306`
- `DB_NAME=event_management`
- `DB_USER=your-mysql-user`
- `DB_PASS=your-mysql-password`

Optional:
- `DATABASE_URL=mysql://USER:PASSWORD@HOST:3306/event_management`

If `DATABASE_URL` is set, the app can read from it. If not, it will use `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, and `DB_PASS`.

## Free Render Deploy Steps

1. Push this repository to GitHub.
2. Create your external MySQL database.
3. Create a database named `event_management` if your provider does not create it automatically.
4. Import `sql/event_management_schema.sql` into that database.
5. Log in to Render.
6. Click `New` and choose `Web Service`.
7. Connect your GitHub repository.
8. Select this repo.
9. Choose:
   - Runtime: `Docker`
   - Plan: `Free`
10. Render will use `render.yaml`, or you can continue with the default Docker detection.
11. Add these environment variables in Render:
   - `APP_BASE_URL`
   - `DB_HOST`
   - `DB_PORT`
   - `DB_NAME`
   - `DB_USER`
   - `DB_PASS`
12. Deploy the service.
13. Open your Render URL after deploy completes.
14. Test:
   - user login
   - admin login
   - event registration
   - invoice preview

## Recommended Values

If your Render URL is:

`https://event-management-web.onrender.com`

Then use:

- `APP_BASE_URL=https://event-management-web.onrender.com`

## Notes

- Free Render web services spin down after inactivity.
- First request after idle may be slow.
- Do not use the old `render.mysql.yaml` file for the free-only setup.
- The app is already configured to run without a local writable invoice folder because invoices are now HTML-based.

## Files Used For Free Deploy

- `Dockerfile`
- `docker/start-apache.sh`
- `render.yaml`
- `config/database.php`

## If Deploy Fails

Check these first:

- wrong database hostname
- database provider blocking Render IPs
- wrong database password
- schema not imported
- `APP_BASE_URL` set incorrectly
