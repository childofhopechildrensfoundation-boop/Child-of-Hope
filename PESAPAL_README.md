# Pesapal Donation System - README

**Status**: ✅ Production Ready

Your Child of Hope Children's Foundation website now has a complete, secure Pesapal donation system with an admin dashboard.

---

## 🎯 Features

✅ **Secure Payments**
- OAuth 1.0a signed API requests
- SSL/HTTPS encryption
- CSRF token protection
- Input validation & sanitization

✅ **Donation Options**
- 5 preset amounts (50K, 100K, 200K, 500K, 1M UGX)
- Custom donation amount (minimum 1,000 UGX)
- Donor name & email required for receipts

✅ **Transaction Management**
- Real-time payment verification
- Automatic receipt emails
- Transaction logging (JSON format)
- Admin dashboard with statistics
- CSV export capability

✅ **Admin Tools**
- Dashboard at `/pesapal_admin.php`
- Password protected
- View all transactions with status
- Statistics: completed, pending, failed donations
- Export data as CSV

✅ **Error Handling**
- Comprehensive error logging
- Graceful failure messages
- Detailed PHP logs for debugging
- Transaction reference tracking

---

## 📂 Files Included

| File | Purpose |
|------|---------|
| `pesapal.php` | Payment initiator (OAuth signing, API calls) |
| `pesapal_callback.php` | Webhook handler (payment verification) |
| `pesapal_admin.php` | Admin dashboard (transaction viewer) |
| `donate.html` | Updated donation page with Pesapal integration |
| `.env` | Configuration (API keys, passwords) - **NOT IN GIT** |
| `.env.example` | Configuration template |
| `pesapal_transactions.log` | Auto-created transaction log |
| `PESAPAL_SETUP_GUIDE.md` | Initial setup & registration instructions |
| `PESAPAL_DEPLOYMENT_GUIDE.md` | Production deployment guide |

---

## 🚀 Quick Start

### 1. Register Pesapal Account (Sandbox)
- Visit https://pesapal.com → Sign up
- Verify email
- Get API keys from Dashboard → Settings → API Keys

### 2. Create `.env` File
In your website root, create `.env`:
```
PESAPAL_SANDBOX=true
PESAPAL_CONSUMER_KEY=your_key_here
PESAPAL_CONSUMER_SECRET=your_secret_here
ADMIN_PASSWORD=changeme123
```

### 3. Test Donations
- Visit `https://your-domain.com/free-nonprofit-website-template/donate.html`
- Fill in name & email
- Click donation button
- Complete payment
- Verify receipt email & admin dashboard

### 4. Deploy to Production
- Complete Pesapal KYC (NGO documents)
- Get production API keys
- Update `.env` with `PESAPAL_SANDBOX=false` and production keys
- Ensure HTTPS enabled
- See `PESAPAL_DEPLOYMENT_GUIDE.md` for detailed steps

---

## 📊 Admin Dashboard

Access: `https://your-domain.com/pesapal_admin.php`

**Features:**
- View all donations with amounts and status
- Statistics: total donations, completed/pending/failed count
- Filter by date range (manual CSV export)
- Export transactions as CSV
- Real-time refresh

**Login**: Use password from `.env` (ADMIN_PASSWORD)

---

## 💰 Donation Amounts (Configurable)

**Current amounts in UGX:**
- 50,000 (~$14 USD)
- 100,000 (~$27 USD)
- 200,000 (~$54 USD)
- 500,000 (~$135 USD)
- 1,000,000 (~$270 USD)
- **Custom amount** (minimum 1,000 UGX)

**To change amounts:** Edit `donate.html` → search `pesapal-amount-btn`

---

## 🔒 Security Features

✅ **Input Validation**
- Email format verification
- Name length validation
- Amount range checking (min 1,000 UGX)

✅ **Data Protection**
- htmlspecialchars() escaping
- filter_var() sanitization
- CSRF tokens for form submissions

✅ **API Security**
- OAuth 1.0a signed requests
- HMAC-SHA1 signature verification
- SSL certificate verification
- Timeout protection (30 seconds)

✅ **Logging**
- All transactions logged (reference, amount, status, timestamp)
- Secure error logging (not exposed to users)
- Transaction audit trail

✅ **Environment**
- `.env` file never committed to Git
- Password protected admin dashboard
- File permissions (600 for `.env`)

---

## 📝 Transaction Log Format

File: `pesapal_transactions.log`

Each line is JSON:
```json
{
  "timestamp": "2026-06-17 12:34:56",
  "tracking_id": "abc123def456",
  "reference": "DONATION_1719049496_a1b2c3d4",
  "amount": "50000",
  "status": "COMPLETED",
  "http_code": 200
}
```

**Status values:**
- `COMPLETED` - Payment successful
- `PENDING` - Payment being processed
- `FAILED` - Payment failed
- `INVALID` - Transaction invalid

---

## ✉️ Email Receipts

**Automatic receipt sent on successful donation:**

```
Subject: Donation Receipt - Child of Hope Children's Foundation

Dear [Donor Name],

Thank you for your generous donation of UGX [amount] to Child of Hope Children's Foundation.

Transaction Details:
Reference: [reference]
Tracking ID: [tracking_id]
Amount: UGX [amount]
Date: [date/time]

Your support makes a real difference in the lives of children in our community.

Best regards,
Child of Hope Children's Foundation Team
childofhopechildrensfoundation@gmail.com
```

---

## 🛠️ Configuration Reference

### Environment Variables

| Variable | Value | Required |
|----------|-------|----------|
| `PESAPAL_SANDBOX` | `true` or `false` | Yes |
| `PESAPAL_CONSUMER_KEY` | Your API key | Yes |
| `PESAPAL_CONSUMER_SECRET` | Your API secret | Yes |
| `ADMIN_PASSWORD` | Your dashboard password | Yes |

### `.env` Example
```bash
# Sandbox (development)
PESAPAL_SANDBOX=true
PESAPAL_CONSUMER_KEY=EnOIU7iJ1EV/ilL7YHvFi3f8Vu/JVAMq
PESAPAL_CONSUMER_SECRET=xIGCZxylKTXtiqLCNX2ahy8w9Yg=
ADMIN_PASSWORD=mySecurePassword123

# Production (live)
PESAPAL_SANDBOX=false
PESAPAL_CONSUMER_KEY=your_live_key_here
PESAPAL_CONSUMER_SECRET=your_live_secret_here
ADMIN_PASSWORD=anotherSecurePassword456
```

---

## 🐛 Troubleshooting

### Q: Where do I check for errors?
**A:** 
- PHP error log: Check your hosting control panel (cPanel, Plesk)
- Transaction log: `/pesapal_transactions.log`
- Admin dashboard: Check recent transactions
- Browser console: Press F12 → Console tab

### Q: Payment not returning to website?
**A:** 
- Verify callback URL in Pesapal dashboard: `https://your-domain.com/pesapal_callback.php`
- Ensure HTTPS is used (not HTTP)
- Check PHP error logs
- Verify `.env` configuration

### Q: Admin dashboard won't load?
**A:**
- Verify URL: `https://your-domain.com/pesapal_admin.php`
- Check password from `.env` (ADMIN_PASSWORD)
- Clear browser cookies and try again
- Check PHP error logs

### Q: Transaction not appearing in admin dashboard?
**A:**
- Verify `pesapal_transactions.log` file exists and is writable
- Check file permissions: `chmod 666 pesapal_transactions.log`
- Check PHP error logs for write errors
- Verify payment actually completed (check Pesapal dashboard)

---

## 📚 Documentation Files

1. **PESAPAL_SETUP_GUIDE.md** - Initial setup & Pesapal registration
2. **PESAPAL_DEPLOYMENT_GUIDE.md** - Production deployment instructions
3. **This README** - Quick reference & features

---

## 🔄 Migration from PayPal

If you were previously using PayPal:
- All PayPal code has been replaced with Pesapal
- Donation amounts converted from USD to UGX (configured in `donate.html`)
- Existing PayPal buttons removed
- Transaction log format different (now JSON instead of CSV)

---

## 📞 Support Resources

- **Pesapal Docs**: https://pesapal.com/api
- **Pesapal Support**: https://pesapal.com/support
- **Your Email**: childofhopechildrensfoundation@gmail.com

---

## ✅ Pre-Launch Checklist

- [ ] `.env` file created with sandbox API keys
- [ ] Test donation completed successfully
- [ ] Email receipt received
- [ ] Admin dashboard accessible
- [ ] Transaction log created and populated
- [ ] Pesapal KYC submitted (for production)
- [ ] Production API keys obtained
- [ ] HTTPS enabled on website
- [ ] `.env` added to `.gitignore`
- [ ] Documentation reviewed

---

## 📈 Success Metrics

After deployment, monitor:
- **Donation count**: Track in admin dashboard
- **Success rate**: View `COMPLETED` vs `FAILED` status
- **Average donation**: Calculate from transaction log
- **Email delivery**: Verify receipts received

---

## 🎉 You're All Set!

Your Pesapal donation system is production-ready. Donors can now contribute via Pesapal on your website.

**Next steps:**
1. Complete Pesapal KYC for production
2. Deploy to live server
3. Switch to production API keys
4. Monitor transactions regularly
5. Export reports monthly

Thank you for using Pesapal for Child of Hope! 💚
