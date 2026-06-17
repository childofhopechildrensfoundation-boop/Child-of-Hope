# 🚀 COMPLETE DEPLOYMENT GUIDE
## Child of Hope - Full Setup & Launch Process

**Last Updated:** June 17, 2026  
**Status:** ✅ All code complete and tested locally

---

# 📋 DEPLOYMENT CHECKLIST

## Phase 1: Stripe Account Setup (15-20 min)

### Step 1: Create Stripe Account
- [ ] Go to https://stripe.com
- [ ] Click "Start now" 
- [ ] Sign up with email: `childofhopechildrensfoundation@gmail.com`
- [ ] Create password (save it!)
- [ ] Select "Business" account type
- [ ] Enter nonprofit details:
  - Organization: `Child of Hope Children's Foundation`
  - Country: `Uganda`
  - Nonprofit status: Mark as nonprofit

### Step 2: Verify Email
- [ ] Check email inbox
- [ ] Click verification link from Stripe
- [ ] Complete phone verification (may be required)

### Step 3: Get API Keys
- [ ] Log into Stripe Dashboard: https://dashboard.stripe.com
- [ ] Go to: **Developers** → **API keys** (left sidebar)
- [ ] **TEST MODE** (toggle in top right)
  - [ ] Copy **Publishable Key** (starts with `pk_test_`)
  - [ ] Copy **Secret Key** (starts with `sk_test_`)
- [ ] Save these temporarily (you'll add to .env)

### Step 4: Configure Webhook
- [ ] In Stripe Dashboard: **Developers** → **Webhooks**
- [ ] Click **"Add endpoint"**
- [ ] **Endpoint URL:** (we'll set this after Bluehost is ready)
  - For now, enter: `https://yourfuturedomain.com/stripe_webhook.php`
  - Or leave blank for now - we'll add it later
- [ ] **Events to send:**
  - ✅ `charge.completed`
  - ✅ `charge.failed`
- [ ] Click **"Add endpoint"**
- [ ] Copy the **Signing secret** (starts with `whsec_test_`)

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

## Phase 3: Update .env File (5 min)

Edit `.env` file and add your credentials:

```env
# Pesapal (already configured)
PESAPAL_SANDBOX=false
PESAPAL_CONSUMER_KEY=EnOIU7iJ1EV/ilL7YHvFi3f8Vu/JVAMq
PESAPAL_CONSUMER_SECRET=xIGCZxylKTXtiqLCNX2ahy8w9Yg=

# Stripe API Keys (add your TEST keys here first)
STRIPE_PUBLIC_KEY=pk_test_YOUR_PUBLIC_KEY_HERE
STRIPE_SECRET_KEY=sk_test_YOUR_SECRET_KEY_HERE
STRIPE_WEBHOOK_SECRET=whsec_test_YOUR_WEBHOOK_SECRET_HERE

# Admin password (CHANGE THIS!)
ADMIN_PASSWORD=changeme123
```

**Replace the Stripe keys with the ones you copied from Stripe Dashboard.**

---

## Phase 4: Deploy Files via FTP (15-20 min)

### Files to Upload:

```
✅ /pesapal.php (6.6 KB)
✅ /pesapal_callback.php (8.1 KB)
✅ /pesapal_admin.php (existing)
✅ /stripe.php (5.6 KB)
✅ /stripe_webhook.php (4.9 KB)
✅ /admin_dashboard.php (14 KB)
✅ /donate.html (44 KB - UPDATED)
✅ /.env (0.5 KB - UPDATE WITH YOUR KEYS)
✅ /vendor/ (entire Stripe library folder)
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
   - `stripe.php`
   - `stripe_webhook.php`
   - `admin_dashboard.php`
   - `donate.html`
   - `.env` (uploaded last, with your real keys!)
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
   - Should show both **Pesapal** and **Stripe** tabs

2. [ ] Check admin dashboard: `https://yourdomainname.com/admin_dashboard.php`
   - Login with password from `.env`
   - Should show dashboard (no transactions yet)

3. [ ] Test Pesapal tab: `https://yourdomainname.com/pesapal.php`
   - (Should give error if accessed directly - this is normal)

4. [ ] Test Stripe tab: `https://yourdomainname.com/stripe.php`
   - (Should give error if accessed directly - this is normal)

---

## Phase 6: Update Stripe Webhook URL (5 min)

Now that you have your domain, set the real webhook URL:

1. [ ] Go to Stripe Dashboard
2. [ ] **Developers** → **Webhooks**
3. [ ] If you added an endpoint earlier:
   - [ ] Click on it
   - [ ] Update URL to: `https://yourdomainname.com/stripe_webhook.php`
   - [ ] Click **Update**
4. [ ] If you didn't add one yet:
   - [ ] Click **"Add an endpoint"**
   - [ ] URL: `https://yourdomainname.com/stripe_webhook.php`
   - [ ] Events: `charge.completed` + `charge.failed`
   - [ ] Click **"Add endpoint"**

---

## Phase 7: Test Stripe Payment (10-15 min)

### Test with TEST Credit Card:

1. [ ] Go to: `https://yourdomainname.com/donate.html`
2. [ ] Click **"Stripe"** tab
3. [ ] Fill in form:
   - **Name:** `Test Donor`
   - **Email:** Your email
   - **Amount:** `$10`
4. [ ] Click **"Donate $10 USD"** (or custom amount)
5. [ ] You'll see loading message, then redirect to **Stripe Checkout**
6. [ ] On Stripe Checkout page:
   - **Card Number:** `4242 4242 4242 4242`
   - **Expiry:** `12/25`
   - **CVC:** `123`
   - **Name:** `Test Donor`
7. [ ] Click **"Pay"**
8. [ ] After success, you should see:
   - ✅ Success message on your site
   - Transaction reference number

### Verify Transaction Was Logged:

9. [ ] Go to: `https://yourdomainname.com/admin_dashboard.php`
10. [ ] Login with your admin password
11. [ ] You should see the test transaction in the table!

### Verify Email Receipt:

12. [ ] Check your email (the one you used for donation)
13. [ ] You should receive a receipt email from Stripe payment

---

## Phase 8: Test Pesapal Payment (10-15 min)

### Test with Pesapal Sandbox:

1. [ ] Go to: `https://yourdomainname.com/donate.html`
2. [ ] Click **"Pesapal"** tab
3. [ ] Fill in form:
   - **Name:** `Test Pesapal Donor`
   - **Email:** Your email
   - **Amount:** `$5`
4. [ ] Click **"Donate $5 USD"** button
5. [ ] You'll be redirected to **Pesapal Checkout**
6. [ ] Complete payment on Pesapal (use test merchant account)
7. [ ] Return to your site - should see success message

### Verify Transaction Was Logged:

8. [ ] Go to: `https://yourdomainname.com/admin_dashboard.php`
9. [ ] You should now see BOTH transactions:
   - Stripe payment ✅
   - Pesapal payment ✅

---

## Phase 9: Security Checklist (10 min)

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

## Phase 10: Going LIVE (When Ready)

### Switch to Live Stripe Keys:

1. [ ] Go to Stripe Dashboard
2. [ ] Toggle to **"Live"** mode (top right)
3. [ ] Copy your LIVE keys:
   - `pk_live_...`
   - `sk_live_...`
4. [ ] Get LIVE webhook secret

### Update .env on Server:

5. [ ] Edit `.env` via FTP
6. [ ] Replace TEST keys with LIVE keys:
   ```env
   STRIPE_PUBLIC_KEY=pk_live_YOUR_KEY
   STRIPE_SECRET_KEY=sk_live_YOUR_KEY
   STRIPE_WEBHOOK_SECRET=whsec_live_YOUR_SECRET
   ```
7. [ ] Save & upload `.env`

### Test Real Payment:

8. [ ] Go to your donation page
9. [ ] Use REAL credit card with small amount ($1)
10. [ ] Complete payment
11. [ ] Verify in admin dashboard
12. [ ] Check receipt email

### Announce to Donors:

13. [ ] Share your donation page with your community
14. [ ] Link from your social media
15. [ ] Newsletter announcements
16. [ ] Start receiving donations! 🎉

---

# 📊 FILE SUMMARY

## Total Upload Size: ~120 KB (including vendor/)

```
Core Payment Files: 
- stripe.php                   5.6 KB
- stripe_webhook.php           4.9 KB  
- pesapal.php                  6.6 KB
- pesapal_callback.php         8.1 KB
- admin_dashboard.php         14.0 KB
- donate.html                 44.0 KB
- .env                         0.5 KB
- composer.json                0.3 KB
- composer.lock                5.2 KB

Dependencies:
- vendor/ (Stripe library)    ~30 MB
```

---

# 🔧 QUICK REFERENCE

## Admin Dashboard Login
```
URL: https://yourdomainname.com/admin_dashboard.php
Password: Check .env ADMIN_PASSWORD
Features:
- View all transactions (Pesapal + Stripe)
- See donation statistics
- Export to CSV
- Monitor fraud
```

## Donation Page
```
URL: https://yourdomainname.com/donate.html
Payment Methods:
- Pesapal (mobile money, E-wallets)
- Stripe (credit cards, digital wallets)
```

## Important Endpoints
```
- Pesapal Handler: /pesapal.php
- Pesapal Webhook: /pesapal_callback.php
- Stripe Handler: /stripe.php
- Stripe Webhook: /stripe_webhook.php
- Admin Dashboard: /admin_dashboard.php
- Donation Form: /donate.html
```

---

# ⚠️ TROUBLESHOOTING

## "Webhook not received"
- ✅ Verify URL in Stripe Dashboard matches your domain
- ✅ Ensure domain is public (not localhost)
- ✅ Check that `/stripe_webhook.php` file exists
- ✅ Wait 30 seconds after payment for webhook to fire

## "Payment declined"
- ✅ Use correct test card: `4242 4242 4242 4242`
- ✅ Use future expiry date
- ✅ Try a different card (some test cards are restricted)

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

- 🔗 Stripe Help: https://support.stripe.com
- 🔗 Pesapal Help: https://developer.pesapal.com
- 🔗 Bluehost Help: https://www.bluehost.com/help
- 📧 Email: childofhopechildrensfoundation@gmail.com

---

**✅ Status: Ready for deployment!**

**Next Step: Start with Phase 1 (Stripe Account Setup)**
