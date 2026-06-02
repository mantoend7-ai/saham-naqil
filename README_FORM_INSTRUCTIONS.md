Deployment and form handling instructions

Summary
- The tracking form in `tracking.html` now posts to `send_form.php` which sends an email to `sales@saham-sa.com`.

Steps to deploy on cPanel
1. Create the email account `sales@saham-sa.com` in cPanel > Email Accounts. Note its mailbox exists.
2. Upload the project files to your `public_html` (or site root) — ensure `tracking.html`, `send_form.php`, `car.svg`, `style.css`, etc., are in the same public folder.
3. Test the form by opening `https://your-domain.com/tracking.html` and submitting the form. The PHP script uses PHP `mail()`.

If emails do not arrive
- Some hosts require authenticated SMTP. If `mail()` does not deliver, either enable authenticated SMTP in PHP (via PHPMailer) or configure your host to allow local mail.
- To use SMTP with credentials (recommended): install PHPMailer and update `send_form.php` to use SMTP with your cPanel SMTP settings (host, port, username, password). If you want, I can add a PHPMailer example.

Security & spam
- `send_form.php` includes a small honeypot field (`hp`) and basic validation. You should add reCAPTCHA for production.
- Keep backups of form submissions (database or CSV) if you want a persistent record.

Help me configure it for you
- If you prefer I update the code to use SMTP (PHPMailer) and you provide SMTP credentials (host, port, username, password), I can add that change and a `.env.example`.

That's it — upload `send_form.php` and `tracking.html` to your cPanel site root and test. If delivery fails, tell me and I'll switch the handler to SMTP with PHPMailer.