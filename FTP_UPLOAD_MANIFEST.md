# 📦 FTP UPLOAD MANIFEST
## Files to Upload to Bluehost

---

## ✅ PAYMENT SYSTEM FILES (Required)

### Core PHP Files:
```
/pesapal.php                    ← Pesapal payment processor
/pesapal_callback.php           ← Pesapal webhook handler
/stripe.php                     ← Stripe payment processor
/stripe_webhook.php             ← Stripe webhook handler
/admin_dashboard.php            ← Combined admin dashboard
/pesapal_admin.php              ← Pesapal admin (optional)
```

### Configuration:
```
/.env                           ← CRITICAL: Contains API keys
/composer.json                  ← PHP dependencies
/composer.lock                  ← Dependency lock file
```

### Updated HTML:
```
/donate.html                    ← UPDATED: Pesapal + Stripe tabs
```

### Dependencies:
```
/vendor/                        ← Stripe PHP library (entire folder)
```

---

## 📋 UPLOAD CHECKLIST

Use this when uploading via FTP:

```
📦 Pesapal Files:
  ☐ pesapal.php
  ☐ pesapal_callback.php
  ☐ pesapal_admin.php (optional)

📦 Stripe Files:
  ☐ stripe.php
  ☐ stripe_webhook.php

📦 Admin:
  ☐ admin_dashboard.php

📦 Web:
  ☐ donate.html

📦 Configuration:
  ☐ .env (LAST - contains sensitive keys)
  ☐ composer.json
  ☐ composer.lock

📦 Dependencies:
  ☐ vendor/ (entire folder with Stripe library)
```

---

## 🔒 UPLOAD LOCATIONS

### Directory Structure on Bluehost:
```
/public_html/
├── pesapal.php
├── pesapal_callback.php
├── pesapal_admin.php
├── stripe.php
├── stripe_webhook.php
├── admin_dashboard.php
├── donate.html
├── .env                         ← Set permissions to 600!
├── composer.json
├── composer.lock
└── vendor/                      ← Entire Stripe library
    └── stripe/
        └── stripe-php/
            ├── init.php
            ├── lib/
            └── ... (many files)
```

---

## 📥 FTP UPLOAD INSTRUCTIONS

### Using FileZilla:

1. **Connect to Bluehost:**
   - Host: `ftp.yourdomainname.com`
   - Port: `21`
   - Username: [From Bluehost email]
   - Password: [From Bluehost email]

2. **Navigate to /public_html/**

3. **Upload Files (Drag & Drop):**
   - Drag each PHP file to /public_html/
   - Drag entire vendor/ folder
   - Upload .env LAST

4. **Set Permissions on .env:**
   - Right-click `.env` → File Permissions
   - Set to: `600` or `rw-------`
   - Click OK

---

## ✨ VERIFICATION AFTER UPLOAD

### Check Files Exist:
```
✓ https://yourdomainname.com/donate.html          → Should load
✓ https://yourdomainname.com/admin_dashboard.php → Login page
✓ https://yourdomainname.com/stripe.php          → Error (normal if direct access)
✓ https://yourdomainname.com/pesapal.php         → Error (normal if direct access)
```

### Check HTTPS:
```
✓ https://yourdomainname.com → Green lock 🔒
✗ http://yourdomainname.com  → Should redirect to https://
```

### Test Admin Access:
```
✓ Go to: https://yourdomainname.com/admin_dashboard.php
✓ Login with password from .env
✓ Should show dashboard
```

---

## 🚨 COMMON UPLOAD ISSUES

### Issue: "File not found when accessing"
**Solution:** Check file was uploaded to /public_html/ not a subfolder

### Issue: "Permission denied on .env"
**Solution:** Set .env file permissions to 600 via FTP

### Issue: "Stripe library not found"
**Solution:** Make sure entire vendor/ folder is uploaded (not just individual files)

### Issue: "500 Internal Server Error"
**Solution:** 
1. Check .env has all required keys
2. Check vendor/ folder exists
3. Check file permissions (should be 644 for PHP files)
4. Check server error logs in cPanel

---

## 📊 FILE SIZES FOR REFERENCE

```
pesapal.php                 6.6 KB
pesapal_callback.php        8.1 KB
pesapal_admin.php           7.2 KB
stripe.php                  5.6 KB
stripe_webhook.php          4.9 KB
admin_dashboard.php        14.0 KB
donate.html                44.0 KB
.env                        0.5 KB
composer.json               0.3 KB
composer.lock               5.2 KB
vendor/                    ~30 MB
─────────────────────────────────
TOTAL (with vendor/)       ~126 MB
```

---

## 🔐 FILE PERMISSIONS GUIDE

After uploading, permissions should be:

```
-rw-r--r-- (644)  ← PHP files (pesapal.php, stripe.php, etc.)
-rw-r--r-- (644)  ← HTML files (donate.html)
-rw------- (600)  ← .env (CRITICAL: secure!)
-rw-r--r-- (644)  ← composer.json, composer.lock
drwxr-xr-x (755)  ← vendor/ folder
```

To set via FTP:
- Right-click file
- Select "File Permissions"
- Enter the 3-digit code (644, 600, 755)
- Click OK

---

## 📝 NOTES FOR UPLOADER

**DO:**
✅ Upload vendor/ folder in one operation if possible
✅ Upload .env last (contains sensitive keys)
✅ Set .env to 600 permissions
✅ Verify all files uploaded before testing
✅ Keep .env file secure (never share)

**DON'T:**
❌ Upload .env to public locations
❌ Skip vendor/ folder upload
❌ Leave .env with public read permissions
❌ Upload composer.lock if already exists (overwrite it)
❌ Compress .env or .gitignore files

---

**✅ Ready to upload!**

**Next Steps:**
1. Complete Stripe account setup
2. Get Stripe API keys
3. Update .env with your keys
4. Upload all files to Bluehost
5. Test payments
