# Pesapal Donation System - Production Deployment Guide

## Overview
Your website now has a complete, secure Pesapal donation system with admin dashboard. This guide covers deployment to production with HTTPS.

---

## Pre-Deployment Checklist

- [ ] SSL/HTTPS certificate installed on server
- [ ] `.env` file configured (not committed to git)
- [ ] Pesapal sandbox testing completed successfully
- [ ] Admin dashboard tested
- [ ] Email notifications tested
- [ ] Transaction logging verified
- [ ] Pesapal KYC documentation submitted
- [ ] Production API keys obtained from Pesapal
- [ ] Backup of current website taken

---

## File Structure

```
free-nonprofit-website-template/
├── donate.html                    (Updated with Pesapal form)
├── pesapal.php                    (Payment handler)
├── pesapal_callback.php           (Webhook handler)
├── pesapal_admin.php              (Admin dashboard - NEW)
├── .env                           (Configuration - DO NOT COMMIT)
├── .env.example                   (Template)
├── PESAPAL_SETUP_GUIDE.md         (Setup instructions)
├── PESAPAL_DEPLOYMENT_GUIDE.md    (This file)
├── pesapal_transactions.log       (Auto-created transaction log)
└── [other website files]
```

---

## Step 1: Prepare Server Environment

### 1.1 Requirements
- PHP 7.4+ (8.0+ recommended)
- cURL extension enabled
- File write permissions for transaction logs
- HTTPS/SSL certificate

### 1.2 Verify PHP Extensions
```bash
# SSH into your server and run:
php -m | grep -i curl

# Or check via web:
php -i | grep -i curl
```

### 1.3 Create Directories and Set Permissions
```bash
# Ensure write permissions for transaction logs
chmod 755 /path/to/website/

# If using cPanel/Plesk, use the control panel's file manager
# to verify permissions on the website root
```

---

## Step 2: Configure Environment

### 2.1 Create `.env` File (Production)

**Via SSH/Terminal:**
```bash
cd /path/to/website/free-nonprofit-website-template
cat > .env << 'EOF'
PESAPAL_SANDBOX=false
PESAPAL_CONSUMER_KEY=your_live_consumer_key_here
PESAPAL_CONSUMER_SECRET=your_live_consumer_secret_here
ADMIN_PASSWORD=change_me_to_strong_password
EOF

chmod 600 .env
```

**Via FTP (cPanel File Manager):**
1. Connect to your server via FTP
2. Navigate to `/free-nonprofit-website-template/`
3. Create new file named `.env`
4. Add these lines (replace with your actual keys):
   ```
   PESAPAL_SANDBOX=false
   PESAPAL_CONSUMER_KEY=your_live_consumer_key
   PESAPAL_CONSUMER_SECRET=your_live_consumer_secret
   ADMIN_PASSWORD=change_to_strong_password
   ```
5. Set file permissions to `600` (read/write for owner only)

### 2.2 Verify `.env` is Not Accessible
Test that the file is not readable via HTTP:
```bash
curl https://your-domain.com/.env
# Should return 403 Forbidden or 404 Not Found
```

### 2.3 Add to `.gitignore`
If using Git, ensure `.env` is never committed:
```bash
echo ".env" >> .gitignore
git add .gitignore
git commit -m "Protect .env from version control"
```

---

## Step 3: Update Pesapal Settings

### 3.1 In Pesapal Dashboard
1. Log in to https://www.pesapal.com/dashboard (PRODUCTION)
2. Navigate to **Settings** → **Merchant Details**
3. Update **Callback/Return URL** to:
   ```
   https://your-domain.com/pesapal_callback.php
   ```
4. Verify **API Keys** section shows your production Consumer Key & Secret
5. Ensure webhook notifications are enabled

---

## Step 4: Upload Files to Production

### 4.1 Via FTP (cPanel)
1. Download these files from your local machine:
   - `pesapal.php`
   - `pesapal_callback.php`
   - `pesapal_admin.php`
   - `donate.html` (updated version)

2. Upload to: `/public_html/free-nonprofit-website-template/`

3. Set file permissions:
   - `.php` files: `644`
   - Directories: `755`

### 4.2 Via Git/SSH
```bash
# SSH into server
ssh user@your-domain.com

# Navigate to website directory
cd public_html/free-nonprofit-website-template

# Git pull latest changes
git pull origin main

# Verify .env exists (not in git)
ls -la .env

# Verify permissions
chmod 600 .env
chmod 644 pesapal.php pesapal_callback.php pesapal_admin.php
chmod 644 donate.html
```

---

## Step 5: Test Donations (Production)

### 5.1 Access Admin Dashboard
```
https://your-domain.com/free-nonprofit-website-template/pesapal_admin.php
```
- Login with password from `.env` (ADMIN_PASSWORD)
- Should show empty transaction list initially

### 5.2 Make Test Donation
1. Go to: `https://your-domain.com/free-nonprofit-website-template/donate.html`
2. Fill in:
   - Name: Test Donor
   - Email: your-email@example.com
   - Click: Donate 50,000 UGX (or custom amount)
3. Complete payment on Pesapal (use test credentials if provided)
4. Verify:
   - Redirected back to donate.html with success message
   - Email receipt received
   - Transaction appears in admin dashboard

### 5.3 Verify Transaction Log
```bash
# SSH into server
tail -f pesapal_transactions.log

# Should show JSON entries like:
# {"timestamp":"2026-06-17 12:34:56","tracking_id":"...","reference":"...","amount":"50000","status":"COMPLETED"}
```

---

## Step 6: Security Hardening

### 6.1 Change Admin Password
**In `.env`:**
```
ADMIN_PASSWORD=your_very_strong_random_password_here
```

Generate strong password:
```bash
openssl rand -base64 16
```

### 6.2 Restrict Admin Access (Optional)
Add IP whitelist to `pesapal_admin.php`:

Edit top of `pesapal_admin.php` after `session_start();`:
```php
// IP Whitelist (add your office IPs)
$allowedIPs = ['192.168.1.1', '203.0.113.50'];
if (!in_array($_SERVER['REMOTE_ADDR'], $allowedIPs)) {
    die('Access denied');
}
```

### 6.3 HTTPS/SSL Enforcement
Add to `.htaccess`:
```apache
# Force HTTPS
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Protect .env file
<Files ".env">
    Deny from all
</Files>
```

### 6.4 Update Headers
Add security headers to `donate.html` (in `<head>`):
```html
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; font-src 'self' https:;">
```

---

## Step 7: Monitoring & Maintenance

### 7.1 Monitor Transaction Log
```bash
# SSH into server
wc -l pesapal_transactions.log        # Count transactions
tail -20 pesapal_transactions.log     # View recent transactions
grep FAILED pesapal_transactions.log  # Find failed payments
```

### 7.2 Rotate Logs (Monthly)
```bash
# Archive and rotate log
mv pesapal_transactions.log pesapal_transactions_$(date +%Y%m%d).log
# Create new empty log
touch pesapal_transactions.log
```

### 7.3 Backup Strategy
```bash
# Daily backup of transactions
0 2 * * * cp /path/to/pesapal_transactions.log /backups/pesapal_$(date +\%Y\%m\%d).log
```

### 7.4 Email Alerts
If a payment fails, manually notify donor:
1. Check transaction log for failed reference
2. Find donor email from Pesapal dashboard
3. Send custom follow-up email

---

## Step 8: Production Checklist

- [ ] `.env` file created with production API keys
- [ ] `.env` file permissions set to `600`
- [ ] `.env` added to `.gitignore`
- [ ] All PHP files uploaded with `644` permissions
- [ ] Admin dashboard accessible and password protected
- [ ] Test donation completed successfully
- [ ] Confirmation email received
- [ ] Transaction logged and visible in admin dashboard
- [ ] HTTPS enabled and working
- [ ] Callback URL updated in Pesapal dashboard
- [ ] Error logging enabled in PHP
- [ ] Backup strategy implemented
- [ ] Transaction log rotation scheduled
- [ ] Security headers configured

---

## Troubleshooting

### Issue: "Payment gateway not configured"
**Cause**: `.env` file not found or credentials missing
**Solution**: 
```bash
# Verify .env exists
ls -la .env

# Check contents (safely)
grep PESAPAL_CONSUMER_KEY .env

# Recreate if needed
cat > .env << 'EOF'
PESAPAL_SANDBOX=false
PESAPAL_CONSUMER_KEY=your_key
PESAPAL_CONSUMER_SECRET=your_secret
ADMIN_PASSWORD=your_password
EOF
```

### Issue: Admin dashboard shows "Invalid password"
**Solution**: 
- Verify ADMIN_PASSWORD in `.env` matches what you're typing
- Restart browser session (clear cookies)
- Check for extra spaces in `.env`

### Issue: Transaction log file not created
**Solution**:
```bash
# Check directory permissions
ls -ld /path/to/website/
chmod 755 /path/to/website/

# Create log file manually
touch pesapal_transactions.log
chmod 666 pesapal_transactions.log
```

### Issue: "oauth_signature does not match"
**Cause**: API credentials are incorrect or expired
**Solution**:
- Log into Pesapal dashboard
- Verify Consumer Key and Secret haven't changed
- Regenerate keys if needed
- Update `.env` with new keys

### Issue: Callback URL not working (Payment not returning)
**Solution**:
- Verify URL in Pesapal dashboard: `https://your-domain.com/pesapal_callback.php`
- Ensure HTTPS is used (not HTTP)
- Check PHP error logs for errors
- Test manually: `curl -v https://your-domain.com/pesapal_callback.php?pesapal_tracking_id=123&pesapal_merchant_reference=test`

---

## Support

- **Pesapal Docs**: https://pesapal.com/api
- **Pesapal Support**: https://pesapal.com/support
- **Email**: childofhopechildrensfoundation@gmail.com

---

## Security Notes

🔒 **Important**: 
- Never commit `.env` to version control
- Change default admin password immediately
- Rotate API keys annually or if compromised
- Keep error logs but don't expose them to public
- Enable HTTPS only (no HTTP)
- Use strong passwords (18+ characters)
- Monitor transaction logs regularly

---

## What's Included

✅ OAuth 1.0a signed API requests  
✅ SSL certificate verification  
✅ Input validation & sanitization  
✅ CSRF token protection  
✅ Comprehensive error logging  
✅ Transaction tracking  
✅ Automatic email receipts  
✅ Admin dashboard with statistics  
✅ CSV export functionality  
✅ Production-ready security

Enjoy your Pesapal donations system! 🎉
