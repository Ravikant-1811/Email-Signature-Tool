# Email Signature Tool

A modern, database-backed email signature generator built with PHP + SQLite. Upload photos, generate email-safe HTML, and store signatures for reuse.

## Features
- Modern UI with live preview
- Upload photo + banner images
- Save signatures to SQLite
- Load saved signatures
- Copy HTML for email clients

## Requirements
- PHP 7.4+ (with PDO SQLite enabled)

## Run Locally
From the project folder:

```bash
php -S localhost:8002 -t .
```

Then open:
`http://localhost:8002/index.php`

## Project Structure
- `index.php` — UI
- `app.js` — front-end logic
- `api.php` — SQLite API + upload handler
- `styles.css` — UI styles
- `uploads/` — uploaded images (ignored in git)
- `storage/` — SQLite database (ignored in git)

## Notes
- Uploaded images are stored in `uploads/`.
- SQLite DB is created automatically in `storage/`.
