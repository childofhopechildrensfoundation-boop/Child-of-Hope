# Real Testing Guide - Pesapal Integration
## Step-by-Step Production Test

**Status**: Production credentials configured  
**Ready For**: Live payment testing  
**Goal**: Verify end-to-end donation processing

---

## 📋 Pre-Test Checklist

Before testing with real money, verify:

- [ ] Production API keys configured in `.env` ✅ (Done)
- [ ] Domain name ready
- [ ] Web hosting with cPanel or similar
- [ ] HTTPS/SSL certificate available
- [ ] FTP/SFTP access to hosting
- [ ] Pesapal account logged in

---

## Step 1: Upload Files to Web Server

### Using FTP/SFTP:

1. **Connect to your hosting**
   - Host: your-hosting-provider.com
   - Username: Your cPanel username
   - Password: Your cPanel password
   - Port: 21 (FTP) or 22 (SFTP)

2. **Navigate to public_html or www folder**

3. **Upload these files:**
   ```
   pesapal.php
   pesapal_callback.php
   pesapal_admin.php
   donate.html
   .env
   ```

4. **Create .env file** if not uploaded:
   ```
   PESAPAL_SANDBOX=false
   PESAPAL_CONSUMER_KEY=EnOIU7iJ1EV/ilL7YHvFi3f8Vu/JVAMq
   PESAPAL_CONSUMER_SECRET=xIGCZxylKTXtiqLCNX2ahy8w9Yg=
   ADMIN_PASSWORD=changeme123
   ```

5. **Set file permissions** via SSH/Terminal:
   ```bash
   chmod 644 pesapal.php
   chmod 644 pesapal_callback.php
   chmod 644 pesapal_admin.php
   chmod 644 donate.html
   chmod 600 .env
   ```

---

## Step 2: Enable HTTPS/SSL

Your hosting likely has free Let's Encrypt SSL.

### Via cPanel:
1. Log in to cPanel
2. Find "AutoSSL" or "SSL/TLS Certificate"
3. Click "Issue Certificate" for your domain
4. Wait 5-15 minutes for activation
5. Verify HTTPS works: `https://your-domain.com`

### Verify SSL:
```bash
curl -I https://your-domain.com
```
Should show: `HTTP/2 200` ✅

---

## Step 3: Configure Pesapal Callback URL

1. **Log in to Pesapal Dashboard**
   - https://www.pesapal.com/dashboard/
   - Email: childofhopechildrensfoundation@gmail.com
   - Password: Your password

2. **Navigate to IPN Settings**
   - Menu → Account Settings → IPN Settings

3. **Add Callback URL**
   - Website Domain: `your-domain.com`
   - IPN Listener URL: `https://your-domain.com/pesapal_callback.php`
   - Click "Save URL"

4. **Verify it saved** ✅

---

## Step 4: Test Admin Dashboard

**Before testing payments**, make sure admin dashboard works:

1. **Visit**: `https://your-domain.com/pesapal_admin.php`
2. **Login**: 
   - Password: `changeme123` (or your custom password)
3. **Should see**:
   - ✅ Dashboard loads
   - ✅ Statistics cards (0 transactions)
   - ✅ "No transactions yet" message
   - ✅ All buttons functional

If admin dashboard works, everything is configured correctly!

---

## Step 5: Make Test Payment

### First Test: Small Amount (1,000 UGX)

1. **Visit Donate Page**: `https://your-domain.com/donate.html`

2. **Fill Form**:
   - Your Full Name: `Test Donor`
   - Your Email Address: `test@childofhope.org`
   - Select: `Donate 50,000 UGX` (or use custom: `1,000`)

3. **Click Donate Button**

4. **You should be redirected to Pesapal payment page**
   - Complete payment with valid mobile money
   - Phone number (M-Pesa, Airtel Money, etc.)
   - Enter PIN to confirm

5. **After successful payment**:
   - You'll be redirected back to `donate.html`
   - Should see: "Payment processing..." or success message
   - **Check 2-3 minutes for callback**

---

## Step 6: Verify Transaction

### Check Admin Dashboard:

1. **Visit**: `https://your-domain.com/pesapal_admin.php`
2. **Login** with password
3. **Should see**:
   - ✅ Statistics updated (1 completed or pending)
   - ✅ Transaction appears in table
   - ✅ Shows: Reference, Amount, Status, Tracking ID

### Check Email:

1. **Check**: `test@childofhope.org` for receipt email
   - Should contain: Transaction reference, amount, tracking ID
   - ✅ Email confirmation working

### Check Transaction Log:

Via SSH/Terminal:
```bash
tail pesapal_transactions.log
```
Should show JSON entry with:
```json
{
  "timestamp": "2026-06-17 14:30:45",
  "tracking_id": "XXXXX",
  "reference": "DONATION_1718635445_abcd1234",
  "amount": 50000,
  "status": "COMPLETED"
}
```

---

## Step 7: Make Second Test (Larger Amount)

Once first test succeeds, test with larger amount:

1. **Visit Donate Page** again
2. **Enter different donor**: `David Okello`
3. **Use custom amount**: `250,000 UGX`
4. **Complete payment**
5. **Verify**:
   - ✅ Transaction appears in admin dashboard
   - ✅ Receipt email sent
   - ✅ Amount shows correctly

---

## 🎯 Success Criteria

**Everything working if you see:**

✅ Donation page loads with Pesapal form  
✅ Payment processes through Pesapal  
✅ Redirected back to your website  
✅ Transaction appears in admin dashboard  
✅ Statistics update correctly  
✅ Receipt email sends automatically  
✅ Transaction log creates entries  

---

## 🆘 Troubleshooting

### "Payment page shows error"
- ✅ Check callback URL is correct in Pesapal dashboard
- ✅ Verify HTTPS is working
- ✅ Check .env has correct API keys
- ✅ Verify pesapal_callback.php exists on server

### "Transaction doesn't appear in dashboard"
- ✅ Wait 3-5 minutes for Pesapal callback
- ✅ Refresh admin dashboard
- ✅ Check server error logs: `tail -f /var/log/apache2/error.log`
- ✅ Verify file permissions: `chmod 600 .env`

### "Receipt email not sending"
- ✅ Check your mail() function is enabled (PHP setting)
- ✅ Verify test email address is correct
- ✅ Check spam/promotions folder
- ✅ Contact hosting support for mail configuration

### "Admin dashboard says wrong password"
- ✅ Check .env ADMIN_PASSWORD value
- ✅ Make sure you updated it (default is `changeme123`)
- ✅ Check for extra spaces in .env

### "HTTPS shows warning"
- ✅ Wait for Let's Encrypt to fully activate (15 min)
- ✅ Force refresh browser (Ctrl+Shift+R)
- ✅ Clear browser cache
- ✅ Check SSL is auto-renewing in cPanel

---

## 📊 Test Transaction Examples

### Example 1: Successful Test
```
Time: 14:35:22
Donor: Test Donor (test@childofhope.org)
Amount: 50,000 UGX
Status: COMPLETED
Email: ✅ Sent
Dashboard: ✅ Shows transaction
Log: ✅ Recorded
```

### Example 2: Failed Payment
```
Time: 14:40:10
Donor: Jane Smith (jane@example.com)
Amount: 100,000 UGX
Status: FAILED (user cancelled)
Email: ✅ Notification sent
Dashboard: ✅ Shows as FAILED
Log: ✅ Recorded with status
```

---

## 🔐 Security Notes for Testing

1. **Use test phone numbers**
   - Real charges will occur!
   - Use smallest amounts (1,000-5,000 UGX)
   - Only you/your team should test

2. **Change Admin Password**
   - Default: `changeme123`
   - Change to something strong
   - Store in password manager

3. **Protect .env File**
   - Never commit to GitHub
   - Keep backups safe
   - Set permissions to 600 (read-only)

4. **Monitor Early Transactions**
   - Watch first few real donations
   - Verify emails send correctly
   - Monitor for errors in logs

---

## ✅ Post-Test Checklist

After successful testing:

- [ ] Transaction appears in admin dashboard
- [ ] Receipt email received by donor
- [ ] Amount matches what was paid
- [ ] Transaction log shows entry
- [ ] Statistics update correctly
- [ ] Admin dashboard is secure
- [ ] HTTPS working properly
- [ ] Callback URL confirmed working
- [ ] Password changed from default
- [ ] Ready for live donors!

---

## 🚀 Now What?

After successful test:

1. **Announce to donors** - Website is ready for donations!
2. **Test with team** - Have 2-3 people test
3. **Monitor first week** - Watch transaction logs
4. **Optimize** - Adjust donation amounts, messaging
5. **Scale** - Promote on social media, email

---

## 📞 Support

If you get stuck:

**For Pesapal issues:**
- Email: support@pesapal.com
- Website: https://www.pesapal.com/support

**For technical issues:**
- Check error logs on server
- Verify file permissions
- Test pesapal.php directly
- Check mail server configuration

---

## Key URLs for Testing

```
Donation Page: https://your-domain.com/donate.html
Admin Dashboard: https://your-domain.com/pesapal_admin.php
Payment Handler: https://your-domain.com/pesapal.php
Callback Handler: https://your-domain.com/pesapal_callback.php

Pesapal Dashboard: https://www.pesapal.com/dashboard/
IPN Settings: https://www.pesapal.com/dashboard/merchant/merchantipn
```

---

**Next Step**: Tell me your domain name and I can help verify everything is working! 🎉
