# Stripe Integration Setup Guide

## Step 1: Create a Stripe Account

1. Go to [https://stripe.com](https://stripe.com)
2. Click **"Start now"** or **"Sign up"**
3. Enter your email and create a password
4. Choose **"Business"** as your account type
5. Fill in your nonprofit information:
   - Organization name: `Child of Hope Children's Foundation`
   - Country: Uganda
   - Nonprofit status (optional but recommended for better rates)
6. Verify your email address

---

## Step 2: Get Your API Keys

1. Log in to your Stripe Dashboard: [https://dashboard.stripe.com](https://dashboard.stripe.com)
2. Go to **"Developers"** → **"API keys"** (left sidebar)
3. You'll see two types of keys:
   - **Publishable key** (starts with `pk_`)
   - **Secret key** (starts with `sk_`)
   
4. **For Development/Testing:**
   - Use "**Test**" mode keys (toggle in top right)
   - These start with `pk_test_` and `sk_test_`

5. **For Production:**
   - Switch to "**Live**" mode
   - Use `pk_live_` and `sk_live_` keys

---

## Step 3: Set Up Webhook (For Payment Confirmation)

1. In Stripe Dashboard, go to **"Developers"** → **"Webhooks"** (left sidebar)
2. Click **"Add an endpoint"** button
3. In the **"Endpoint URL"** field, enter:
   ```
   https://yourdomainname.com/stripe_webhook.php
   ```
   *Replace `yourdomainname.com` with your actual domain (from Bluehost)*

4. Select events to listen for:
   - Under **"Select events to send to this endpoint"**, search for and select:
     - ✅ `charge.completed` (successful payment)
     - ✅ `charge.failed` (failed payment)

5. Click **"Add endpoint"**

6. You'll see a **Webhook signing secret** (starts with `whsec_`)
   - Copy this secret

---

## Step 4: Update Your .env File

Edit the `.env` file in your project root and add your Stripe credentials:

```env
# Stripe API Keys (get from https://dashboard.stripe.com/apikeys)
STRIPE_PUBLIC_KEY=pk_test_YOUR_ACTUAL_PUBLIC_KEY
STRIPE_SECRET_KEY=sk_test_YOUR_ACTUAL_SECRET_KEY
STRIPE_WEBHOOK_SECRET=whsec_test_YOUR_ACTUAL_WEBHOOK_SECRET
```

**Example (TEST KEYS - for development only):**
```env
STRIPE_PUBLIC_KEY=pk_test_51PqmH5K1RqQqRqQqRqQq1234
STRIPE_SECRET_KEY=sk_test_51PqmH5K1RqQqRqQqRqQq5678
STRIPE_WEBHOOK_SECRET=whsec_test_26q0l3w9r1q2w3e4r5t6y7u8i9o0p1q2
```

---

## Step 5: Test Stripe Payment (Development)

1. Go to your donation page: `http://localhost:8000/donate.html`
2. Click the **"Stripe"** tab
3. Fill in test donor details:
   - Name: Test Donor
   - Email: test@example.com
   - Amount: $5

4. Click **"Donate"** button
5. You'll be redirected to Stripe Checkout
6. Use these **test card numbers**:
   - ✅ Successful: `4242 4242 4242 4242`
   - ❌ Failed: `4000 0000 0000 0002`
   - 📱 3D Secure: `4000 0025 0000 3155`

7. Use any future date for expiry (e.g., `12/25`)
8. Use any 3-digit CVC (e.g., `123`)

9. Click **"Pay"** button
10. You should see a success message!

---

## Step 6: Verify Transaction Logging

After a successful test payment:

1. Check transaction log: `stripe_transactions.log`
2. Look in your project directory - it should contain JSON entries like:
   ```json
   {
     "timestamp": "2026-06-17 10:30:45",
     "charge_id": "ch_1Pq1q1q1q1q1q1q1",
     "reference": "DONATION_1718601045_a1b2c3d4",
     "amount": 5.00,
     "status": "COMPLETED",
     "donor_name": "Test Donor",
     "donor_email": "test@example.com",
     "cause": "General Donation",
     "http_code": 200
   }
   ```

3. Check admin dashboard to see the transaction listed

---

## Step 7: Production Deployment

When you're ready to go LIVE with real payments:

1. **Switch to Live Mode:**
   - Log into Stripe Dashboard
   - Toggle to "Live" mode (top right)
   - Copy your LIVE keys (`pk_live_` and `sk_live_`)

2. **Update .env:**
   ```env
   STRIPE_PUBLIC_KEY=pk_live_YOUR_ACTUAL_PUBLIC_KEY
   STRIPE_SECRET_KEY=sk_live_YOUR_ACTUAL_SECRET_KEY
   STRIPE_WEBHOOK_SECRET=whsec_live_YOUR_ACTUAL_WEBHOOK_SECRET
   ```

3. **Update Webhook URL:**
   - Add another endpoint in Stripe Dashboard → Webhooks
   - Use your LIVE domain: `https://yourdomainname.com/stripe_webhook.php`

4. **Test Real Payment:**
   - Use your actual credit card
   - First transaction will process but may hold for verification
   - Stripe may need additional nonprofit documentation

---

## Stripe Features Available

✅ **Supported Payment Methods:**
- Credit cards (Visa, Mastercard, American Express, Discover)
- Digital wallets (Apple Pay, Google Pay)
- Bank transfers
- And more...

✅ **Built-in Security:**
- PCI compliance (handled by Stripe)
- SSL/TLS encryption
- Fraud detection

✅ **Features:**
- Automatic receipt emails
- Transaction history
- Export to CSV/PDF
- Refund capabilities
- Multi-currency support (if needed)

---

## Troubleshooting

### Issue: "Payment gateway not configured"
**Solution:** Check your `.env` file has correct keys and file is readable

### Issue: "Webhook signature verification failed"
**Solution:** 
1. Make sure webhook secret in `.env` matches Stripe Dashboard
2. Your domain must be publicly accessible for webhooks

### Issue: Payment redirects but doesn't complete
**Solution:**
1. Check that `stripe_webhook.php` is accessible
2. Verify webhook URL in Stripe Dashboard points to correct path
3. Check server logs for errors

### Issue: "This business account is not available"
**Solution:** Your Stripe account may need additional verification. Contact Stripe support.

---

## Support

- 🔗 Stripe Documentation: [https://stripe.com/docs](https://stripe.com/docs)
- 💬 Stripe Support: [https://support.stripe.com](https://support.stripe.com)
- 📧 Contact Stripe: support@stripe.com

---

## Files Modified

- ✅ `stripe.php` - Payment handler
- ✅ `stripe_webhook.php` - Webhook handler  
- ✅ `donate.html` - Added Stripe tab
- ✅ `.env` - Added Stripe credentials
- ✅ `composer.json` - Added Stripe PHP library

---

**Status:** ✅ Stripe integration complete and ready to use!

Next step: Deploy to Bluehost and configure webhook URL for live payments.
