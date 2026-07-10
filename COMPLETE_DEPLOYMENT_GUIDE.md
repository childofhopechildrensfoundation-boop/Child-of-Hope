# 🚀 COMPLETE DEPLOYMENT GUIDE
## Child of Hope - Full Setup & Launch Process

**Last Updated:** June 17, 2026  
**Status:** ✅ All code complete and tested locally

---

# 📋 DEPLOYMENT CHECKLIST

## Phase 1: Verify Pesapal Configuration (5 min)

Your Pesapal account is already configured with:
- Consumer Key: `EnOIU7iJ1EV/ilL7YHvFi3f8Vu/JVAMq`
- Consumer Secret: `xIGCZxylKTXtiqLCNX2ahy8w9Yg=`

These credentials are already in your `.env` file. No additional setup needed!

---

## Phase 2: Bluehost Signup & Setup (20-30 min)

### Step 1: Choose Your Domain
- [ ] Decide domain name:
  - Option A: `childofhope.org` (new)
  - Option B: `childofhopeug.org` (new)
  - Option C: Use existing domain if you have one
- [ ] Check availability at Bluehost

### Step 2: Complete Bluehost Signup
- [ ] Go to: https://www.bluehost.com/pricing
- [ ] Click **"Get Started"** on Starter plan ($3.99/month)
- [ ] **Create Account:**
  - Email: `childofhopechildrensfoundation@gmail.com`
  - Password: Strong password (save it!)
- [ ] **Domain Setup:**
  - [ ] Select: Register new domain
  - [ ] Enter domain name (e.g., `childofhope.org`)
  - [ ] First year is FREE with Bluehost
- [ ] **Choose Plan:**
  - [ ] Select: **3-Year plan** (best value: $3.99/mo = $143.64 total)
  - [ ] Or select: 1-Year plan if preferred
- [ ] **Payment:**
  - [ ] Enter credit card information
  - [ ] Check "Auto-renewal" box
  - [ ] Complete payment

### Step 3: Receive Credentials
- [ ] Check email for Bluehost confirmation
- [ ] You'll receive:
  - FTP/SFTP username
  - FTP/SFTP password
  - Server address (ftp.yourdomainname.com)
  - cPanel username & password
- [ ] **SAVE THESE** - you'll need them for uploading files

### Step 4: Access cPanel
- [ ] Log into cPanel: https://yourdomainname.com:2083
- [ ] (Or go to Bluehost dashboard and click "cPanel")
- [ ] Username: Your Bluehost account username
- [ ] Password: Your Bluehost password

### Step 5: Verify SSL Certificate
- [ ] In cPanel: Search for **"AutoSSL"** or **"SSL/TLS"**
- [ ] Click on it
- [ ] You should see your domain listed
- [ ] Status should be: ✅ "Active" or "Pending"
- [ ] (Takes 5-15 minutes to activate)
- [ ] Once active, HTTPS will work automatically

---

## Phase 3: Update .env File (1 min)

Your `.env` file is already configured with Pesapal credentials:

```env
PESAPAL_SANDBOX=false
PESAPAL_CONSUMER_KEY=EnOIU7iJ1EV/ilL7YHvFi3f8Vu/JVAMq
PESAPAL_CONSUMER_SECRET=xIGCZxylKTXtiqLCNX2ahy8w9Yg=

# Admin password (CHANGE THIS!)
ADMIN_PASSWORD=changeme123
```

No additional configuration needed - Pesapal credentials are already set!

---

## Phase 4: Deploy Files via FTP (15-20 min)

### Files to Upload:

```
✅ /pesapal.php (6.6 KB)
✅ /pesapal_callback.php (8.1 KB)
✅ /pesapal_admin.php (existing)
✅ /admin_dashboard.php (14 KB)
✅ /donate.html (44 KB - UPDATED)
✅ /.env (0.5 KB)
✅ /vendor/ (PHP dependencies folder)
✅ /composer.json (updated)
✅ /composer.lock (dependency lock file)
```

### Using FileZilla (Recommended FTP Client)

**Download:** https://filezilla-project.org/

1. [ ] Open FileZilla
2. [ ] Go to: **File** → **Site Manager**
3. [ ] Click **"New Site"**
4. [ ] Enter:
   - **Host:** `ftp.yourdomainname.com` (from Bluehost email)
   - **Port:** `21`
   - **Protocol:** `FTP`
   - **Logon Type:** `Normal`
   - **User:** Your FTP username (from Bluehost)
   - **Password:** Your FTP password (from Bluehost)
5. [ ] Click **"Connect"**

**Upload Process:**

6. [ ] Navigate to: `/public_html/` (right side)
7. [ ] On left side, navigate to your project folder locally
8. [ ] Select all payment files (drag & drop to right panel):
   - `pesapal.php`
   - `pesapal_callback.php`
   - `admin_dashboard.php`
   - `donate.html`
   - `.env`
   - `composer.json`
   - `composer.lock`
9. [ ] Drag entire `vendor/` folder to upload
10. [ ] Wait for all files to upload (progress bar)

**Set File Permissions:**

11. [ ] Right-click `.env` file on server
12. [ ] Select **"File permissions"** (or **"Attributes"**)
13. [ ] Set to: `600` or `rw-------`
14. [ ] This ensures only your server can read it
15. [ ] Click **OK**

---

## Phase 5: Verify Files on Server (5 min)

### Check Files Uploaded Successfully:

1. [ ] Open browser: `https://yourdomainname.com/donate.html`
   - Should load ✅ with donation form
   - Should show **Pesapal** tab

2. [ ] Check admin dashboard: `https://yourdomainname.com/admin_dashboard.php`
   - Login with password from `.env`
   - Should show dashboard (no transactions yet)

3. [ ] Test Pesapal tab: `https://yourdomainname.com/pesapal.php`
   - (Should give error if accessed directly - this is normal)

---

## Phase 6: Test Pesapal Payment (10-15 min)

### Test with Pesapal:

1. [ ] Go to: `https://yourdomainname.com/donate.html`
2. [ ] Fill in form:
   - **Name:** `Test Donor`
   - **Email:** Your email
   - **Amount:** `$5`
3. [ ] Click **"Donate $5 USD"** button
4. [ ] You'll be redirected to **Pesapal Gateway**
5. [ ] Complete payment on Pesapal 
6. [ ] Return to your site - should see success message

### Verify Transaction Was Logged:

7. [ ] Go to: `https://yourdomainname.com/admin_dashboard.php`
8. [ ] Login with your admin password
9. [ ] You should see the test transaction in the table!

### Verify Email Receipt:

10. [ ] Check your email (the one you used for donation)
11. [ ] You should receive a receipt email

---

## Phase 7: Security Checklist (10 min)

### Before Going Public:

- [ ] **Change Admin Password:**
  - [ ] Edit `.env` on server via FTP
  - [ ] Change `ADMIN_PASSWORD=changeme123` to a strong password
  - [ ] Save & upload

- [ ] **Verify HTTPS:**
  - [ ] Go to your domain: `https://yourdomainname.com`
  - [ ] Check browser - should show green lock 🔒
  - [ ] Never show HTTP (without S)

- [ ] **Test HTTP Redirect:**
  - [ ] Try: `http://yourdomainname.com` (without S)
  - [ ] Should automatically redirect to HTTPS ✅

- [ ] **Check .env Permissions:**
  - [ ] Via FTP, right-click `.env`
  - [ ] Should be `600` or `rw-------` ✅

- [ ] **Monitor Logs:**
  - [ ] Check logs monthly for suspicious activity
  - [ ] Review transaction logs for fraud

---

## Phase 8: Going LIVE (When Ready)

### Your system is ready for production!

1. [ ] Verify all settings are correct in `.env`
2. [ ] Test with a small real payment ($1 USD)
3. [ ] Verify transaction in admin dashboard
4. [ ] Check receipt email is sent correctly
5. [ ] Announce to your community!

### Start Receiving Donations:

6. [ ] Share your donation page link
7. [ ] Link from social media (Instagram, LinkedIn)
8. [ ] Newsletter announcements
9. [ ] Start helping more children! 🎉

---

# 📊 FILE SUMMARY

## Total Upload Size: ~500 KB (including vendor/)

```
Core Payment Files: 
- pesapal.php                  6.6 KB
- pesapal_callback.php         8.1 KB
- admin_dashboard.php         14.0 KB
- donate.html                 44.0 KB
- .env                         0.5 KB
- composer.json                0.3 KB
- composer.lock                5.2 KB

Dependencies:
- vendor/ (PHP libraries)     ~500 KB
```

---

# 🔧 QUICK REFERENCE

## Admin Dashboard Login
```
URL: https://yourdomainname.com/admin_dashboard.php
Password: Check .env ADMIN_PASSWORD
Features:
- View all Pesapal transactions
- See donation statistics
- Export to CSV
- Monitor donations
```

## Donation Page
```
URL: https://yourdomainname.com/donate.html
Payment Methods:
- Pesapal (mobile money, E-wallets, cards)
```

## Important Endpoints
```
- Pesapal Handler: /pesapal.php
- Pesapal Webhook: /pesapal_callback.php
- Admin Dashboard: /admin_dashboard.php
- Donation Form: /donate.html
```

---

# ⚠️ TROUBLESHOOTING

## "Payment not received"
- ✅ Wait 30 seconds for Pesapal to process
- ✅ Check your Pesapal dashboard
- ✅ Verify webhook is configured correctly

## "Transaction not in dashboard"
- ✅ Wait 5-10 seconds for webhook to process
- ✅ Refresh page
- ✅ Check server error logs

## "HTTPS not working"
- ✅ Wait 5-15 minutes after signup
- ✅ Go to cPanel → SSL/TLS Manager
- ✅ Check if AutoSSL certificate is active
- ✅ Force HTTPS redirect in .htaccess

## ".env file permissions error"
- ✅ Set file permissions to `600` via FTP
- ✅ Right-click → File Permissions → `600`

---

# 📞 SUPPORT

- 🔗 Pesapal Help: https://developer.pesapal.com
- 🔗 Pesapal Dashboard: https://www.pesapal.com
- 🔗 Bluehost Help: https://www.bluehost.com/help
- 📧 Email: childofhopechildrensfoundation@gmail.com

---

**✅ Status: Ready for deployment!**

**Next Step: Start with Phase 1 (Pesapal Verification)**
