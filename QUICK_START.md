# ⚡ QUICK START - 3-STEP DEPLOYMENT

## 🎯 Your 3 Main Tasks:

### 1️⃣ PESAPAL ACCOUNT (if not already configured)
- [ ] Verify Pesapal account is set up
- [ ] Confirm credentials in .env file
- [ ] Test payment gateway access

### 2️⃣ BLUEHOST HOSTING (25 min)
- [ ] Sign up at https://www.bluehost.com/pricing
- [ ] Get FTP credentials via email
- [ ] Set domain to public HTTPS

### 3️⃣ UPLOAD FILES (20 min)
- [ ] Download FileZilla: https://filezilla-project.org/
- [ ] Connect to Bluehost via FTP
- [ ] Upload all payment files to /public_html/
- [ ] Set .env permissions to 600

---

## 🔑 CREDENTIALS TRACKER

**Save these when you get them:**

```
BLUEHOST:
Domain:          ________________
FTP Host:        ftp.________________.com
FTP Username:    ________________
FTP Password:    ________________
cPanel URL:      ________________
cPanel Password: ________________

PESAPAL:
Consumer Key:    ________________
Consumer Secret: ________________
```

---

## ✅ BLUEHOST SETUP QUICK STEPS

### Before uploading files, ensure:
- [ ] Domain is set up and active
- [ ] SSL/HTTPS certificate is active (green lock 🔒)
- [ ] FTP access works in FileZilla
- [ ] /public_html/ directory is accessible

### Command to test FTP (if you prefer Terminal):
```bash
# Connect to FTP
ftp ftp.yourdomainname.com
# Login with credentials from Bluehost

# Check you're in public_html
pwd

# List files
ls

# Upload file
put pesapal.php

# Disconnect
bye
```

---

## 🔄 PAYMENT FLOW DIAGRAM

```
DONOR VISITS SITE
         ↓
https://yourdomainname.com/donate.html
         ↓
    [CHOOSES PAYMENT METHOD]
    ↙                      ↘
PESAPAL TAB              STRIPE TAB
    ↓                      ↓
pesapal.php          stripe.php
    ↓                      ↓
Pesapal.com         Stripe Checkout
    ↓                      ↓
Process Payment      Process Payment
    ↓                      ↓
Callback Received    Webhook Received
    ↓                      ↓
pesapal_callback.php  stripe_webhook.php
    ↓                      ↓
Log Transaction      Log Transaction
    ↓                      ↓
Send Receipt Email   Send Receipt Email
    ↓                      ↓
SUCCESS ✅           SUCCESS ✅
    ↓                      ↓
Admin can view in admin_dashboard.php
```

---

## 🧪 TESTING STRIPE LOCALLY (BEFORE UPLOAD)

```bash
# Start PHP server
cd /Volumes/Moshe\ Works/MY\ WEBSITES/free-nonprofit-website-template
php -S localhost:8000

# Access in browser
http://localhost:8000/donate.html

# Click Stripe tab
# Use test card: 4242 4242 4242 4242
# Should work!

# Check admin dashboard
http://localhost:8000/admin_dashboard.php
# Should show transaction
```

---

## 📱 MOBILE FRIENDLY CHECK

After deployment, test on phone:
- [ ] Visit: https://yourdomainname.com/donate.html
- [ ] Page loads correctly on mobile
- [ ] Both Pesapal and Stripe tabs work
- [ ] Donation form is readable
- [ ] Buttons are clickable
- [ ] Payment redirects work

---

## 🔍 VERIFY DEPLOYMENT CHECKLIST

### After uploading to Bluehost:

1. **Can access donate page:**
   ```
   ✓ https://yourdomainname.com/donate.html
   - Loads? YES/NO
   - Shows payment tabs? YES/NO
   - Form inputs visible? YES/NO
   ```

2. **Can access admin dashboard:**
   ```
   ✓ https://yourdomainname.com/admin_dashboard.php
   - Login form appears? YES/NO
   - Login with password works? YES/NO
   - Dashboard displays? YES/NO
   ```

3. **HTTPS is working:**
   ```
   ✓ https://yourdomainname.com
   - Green lock visible? YES/NO
   - NO warning messages? YES/NO
   ```

4. **Stripe test payment works:**
   ```
   ✓ Complete full test payment
   - Form submits? YES/NO
   - Redirects to Stripe? YES/NO
   - Payment successful? YES/NO
   - Returns to site? YES/NO
   - Transaction in admin? YES/NO
   ```

5. **Pesapal test payment works:**
   ```
   ✓ Complete full test payment
   - Form submits? YES/NO
   - Redirects to Pesapal? YES/NO
   - Payment successful? YES/NO
   - Returns to site? YES/NO
   - Transaction in admin? YES/NO
   ```

---

## 🎓 TESTING STRIPE CARDS

Keep these handy while testing:

**Success:**
- Card: `4242 4242 4242 4242`
- Result: ✅ Payment succeeds

**Decline:**
- Card: `4000 0000 0000 0002`
- Result: ❌ Payment declined

**3D Secure:**
- Card: `4000 0025 0000 3155`
- Result: 🔐 Requires authentication

**For all test cards:**
- Expiry: Any future date (e.g., 12/25)
- CVC: Any 3 digits (e.g., 123)
- Name: Anything

---

## 📧 EMAIL VERIFICATION

After each test payment:
- [ ] Check inbox for receipt email
- [ ] Should come from Stripe or Pesapal
- [ ] Should include:
  - Donation amount
  - Transaction reference
  - Organization name
  - Date/time

---

## 🆘 IF SOMETHING BREAKS

### Quick Fix Steps:

1. **Check HTTPS:**
   - `https://` NOT `http://`

2. **Check file exists:**
   - Via FTP, verify file is in /public_html/

3. **Check .env permissions:**
   - Set to 600, not 644

4. **Clear browser cache:**
   - Ctrl+Shift+Delete (or Cmd+Shift+Delete on Mac)
   - Clear all

5. **Check Stripe webhook:**
   - In Stripe Dashboard
   - Verify URL is correct
   - Verify endpoint is active

6. **Check server logs:**
   - In cPanel
   - Look for PHP errors

---

## 📞 HELP RESOURCES

**Stripe Help:**
- Dashboard: https://dashboard.stripe.com
- Docs: https://stripe.com/docs
- Support: https://support.stripe.com

**Bluehost Help:**
- Dashboard: https://www.bluehost.com
- Support: https://www.bluehost.com/help
- Live Chat: Available 24/7

**Pesapal Help:**
- Dashboard: https://www.pesapal.com
- Docs: https://developer.pesapal.com
- Support: developer@pesapal.com

---

## ✨ GOING LIVE CHECKLIST

When ready for real payments:

- [ ] Switch Stripe from Test to Live mode
- [ ] Update .env with Live Stripe keys
- [ ] Update Stripe webhook URL (if changed)
- [ ] Test with $1 real payment
- [ ] Verify payment processes successfully
- [ ] Announce to donors!

---

**Status: ✅ Ready to deploy!**

**Your next action: Complete the COMPLETE_DEPLOYMENT_GUIDE.md step by step**
