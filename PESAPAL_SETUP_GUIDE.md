# Pesapal Integration Setup Guide

## Organization Details
- **Organization Name**: Child of Hope Children's Foundation
- **Location**: Nansana Yesu Amala, Kampala Uganda
- **Email**: childofhopechildrensfoundation@gmail.com
- **Phone**: +256 731 751 309
- **Website**: https://[your-domain]
- **Currency**: UGX (Uganda Shilling)
- **Environment**: Sandbox (for testing)

---

## Step 1: Register Pesapal Account

### Go to Pesapal Website
1. Visit https://pesapal.com
2. Look for **"Business"** or **"Developers"** section
3. Click **"Sign Up"** or **"Get Started"**

### Fill in Registration Form
Fill the form with these details:

| Field | Value |
|-------|-------|
| Business/Organization Name | Child of Hope Children's Foundation |
| Business Type | Non-Profit Organization |
| Contact Person Name | [Your Name] |
| Email Address | childofhopechildrensfoundation@gmail.com |
| Phone Number | +256 731 751 309 |
| Country | Uganda |
| Website | https://[your-domain] |
| Business Registration Number | [Your NGO Registration Number - if available] |

### Verify Email
- Pesapal will send a verification email to `childofhopechildrensfoundation@gmail.com`
- Click the verification link in the email
- Log in to your Pesapal dashboard

---

## Step 2: Get API Credentials (Sandbox)

### In Pesapal Dashboard
1. Log in to your Pesapal account
2. Navigate to **Settings** → **API Keys** or **Developer Tools**
3. Switch to **Sandbox** environment
4. You'll see two keys:
   - **Consumer Key** (looks like: `XXXXXXXXXXXXXXXXXXXXXXX`)
   - **Consumer Secret** (looks like: `XXXXXXXXXXXXXXXXXXXXXXX`)

### IMPORTANT: Do NOT Share These Keys
- Keep your Consumer Secret private—never commit it to version control
- Never share it in email or chat

---

## Step 3: Configure Your Website

### Create `.env` File
In the root directory of your website (where `donate.html` is), create a file named `.env`:

```
PESAPAL_SANDBOX=true
PESAPAL_CONSUMER_KEY=your_consumer_key_here
PESAPAL_CONSUMER_SECRET=your_consumer_secret_here
```

**Example** (with fake keys):
```
PESAPAL_SANDBOX=true
PESAPAL_CONSUMER_KEY=a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6
PESAPAL_CONSUMER_SECRET=x1y2z3a4b5c6d7e8f9g0h1i2j3k4l5m6
```

### Load .env in Your PHP Code
At the top of `pesapal.php` and `pesapal_callback.php`, the code already loads these variables using `getenv()`. Make sure your hosting environment supports `.env` files or set these as environment variables in your server configuration.

---

## Step 4: Test Donation Page

### Local Testing
1. Start your local server:
   ```bash
   php -S localhost:8000
   ```

2. Open your browser and go to:
   ```
   http://localhost:8000/donate.html
   ```

3. Fill in:
   - **Your Full Name**: [Test Name]
   - **Your Email Address**: [Your Email]
   - Click **Donate 50,000 UGX** or enter a custom amount

4. You'll be redirected to Pesapal's sandbox payment page
5. Use **Pesapal Test Credentials** (provided on their sandbox page)
6. Complete the test payment

### Payment Test Flow
1. You'll fill out donor details on `donate.html`
2. You'll be redirected to Pesapal's secure payment page
3. After payment (success/fail), you'll be redirected back to `donate.html?status=success/failed`
4. Transactions are logged in `pesapal_transactions.log`

---

## Step 5: Go Live (Production)

### Update `.env` for Production
```
PESAPAL_SANDBOX=false
PESAPAL_CONSUMER_KEY=your_live_consumer_key
PESAPAL_CONSUMER_SECRET=your_live_consumer_secret
```

### Additional Production Requirements
1. **Complete Pesapal KYC (Know Your Customer)**:
   - Pesapal will ask for business registration documents
   - Upload scanned copies of:
     - NGO registration certificate
     - Director/Contact person's ID/Passport
     - Proof of address (utility bill, lease agreement)
     - Bank account details (for payouts)

2. **SSL/HTTPS Required**:
   - Ensure your website is served over HTTPS
   - Pesapal will not accept HTTP callbacks

3. **Update Callback URL**:
   - In Pesapal Dashboard → Settings → Callback URL
   - Set to: `https://your-domain/pesapal_callback.php`

4. **Test in Production**:
   - Pesapal provides test merchant accounts even in production
   - Make small real transactions to verify setup

---

## Files Created

| File | Purpose |
|------|---------|
| `pesapal.php` | Initiates donation transactions |
| `pesapal_callback.php` | Verifies payment and handles webhooks |
| `donate.html` | Updated donation page with Pesapal buttons |
| `.env` | Configuration (create manually) |
| `pesapal_transactions.log` | Transaction log (auto-created) |

---

## Troubleshooting

### Issue: "PESAPAL_CONSUMER_KEY is empty"
**Solution**: Make sure `.env` file is in the correct directory and your hosting supports it. Alternatively, set environment variables in your hosting control panel (cPanel, Plesk, etc.).

### Issue: Payment page not redirecting
**Solution**: Check your `pesapal_transactions.log` for errors. Verify Consumer Key/Secret are correct.

### Issue: "oauth_signature does not match"
**Solution**: This means your API keys are incorrect. Double-check them in the Pesapal dashboard.

### Issue: Callback not working
**Solution**: Ensure your callback URL matches exactly in Pesapal settings. Must be HTTPS in production.

---

## Support
- **Pesapal Docs**: https://pesapal.com/api
- **Pesapal Support**: https://pesapal.com/support
- **Email**: childofhopechildrensfoundation@gmail.com

---

## Donation Amounts (Current Configuration)
- 50,000 UGX (~$14 USD)
- 100,000 UGX (~$27 USD)
- 200,000 UGX (~$54 USD)
- 500,000 UGX (~$135 USD)
- 1,000,000 UGX (~$270 USD)
- **Custom Amount** (minimum 1,000 UGX)

You can modify these in `donate.html` by editing the `data-amount` values.
