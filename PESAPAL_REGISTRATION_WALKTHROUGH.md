# Pesapal Account Registration Walkthrough
## For Child of Hope Children's Foundation (NGO)

**Date**: June 17, 2026  
**Goal**: Register nonprofit organization and get production API keys

---

## 📋 Before You Start - Required Documents

Have these ready before starting:
- [ ] Organization Registration Certificate
- [ ] Tax Identification Number (TIN)
- [ ] Business License/Certification
- [ ] Proof of Address (utility bill, lease agreement)
- [ ] Director/Founder ID (passport, national ID)
- [ ] Bank Account Details
- [ ] Organization Logo (PNG/JPG)
- [ ] Organization Website URL

**Your Information**:
- Organization: Child of Hope Children's Foundation
- Location: Nansana Yesu Amala, Kampala, Uganda
- Email: childofhopechildrensfoundation@gmail.com
- Website: (your website URL)

---

## Step 1: Visit Pesapal Website

**URL**: https://sandbox.pesapal.com/ (for testing first)  
**Production URL**: https://www.pesapal.com/ (after testing)

1. Open browser
2. Navigate to Pesapal homepage
3. Look for "Sign Up" or "Get Started" button

---

## Step 2: Choose Account Type

When prompted, select:
- **Account Type**: Business/Organization
- **Organization Type**: Non-Profit Organization (NGO)
- **Country**: Uganda
- **Currency**: UGX (Ugandan Shilling)

---

## Step 3: Enter Organization Details

Fill in the following:

### Basic Information
```
Organization Name: Child of Hope Children's Foundation
Registration Type: NGO/Non-Profit
Organization Email: childofhopechildrensfoundation@gmail.com
Contact Phone: [Your phone number]
Website: [Your website URL]
```

### Address
```
Physical Address: Nansana Yesu Amala, Kampala, Uganda
Postal Code: [Your postal code]
City: Kampala
Country: Uganda
```

### Legal Representative
```
Full Name: [Your name/Director name]
Position: [Executive Director / Founder]
Email: [Your email]
Phone: [Your phone]
ID Type: National ID / Passport
ID Number: [Your ID number]
```

---

## Step 4: Upload Required Documents

When prompted, upload:

1. **Organization Registration Certificate**
   - File: Scan of NGO registration certificate
   - Format: PDF or JPG
   - Size: Max 5MB

2. **Tax Identification Number (TIN) Certificate**
   - File: TIN registration document
   - Format: PDF or JPG
   - Size: Max 5MB

3. **Business License / Certification**
   - File: Current business license
   - Format: PDF or JPG
   - Size: Max 5MB

4. **Proof of Address**
   - File: Utility bill or lease agreement
   - Format: PDF or JPG
   - Size: Max 5MB
   - **Must show**: Organization name and address

5. **Director/Representative ID**
   - File: Front and back of ID
   - Format: PDF or JPG
   - Size: Max 5MB

6. **Organization Logo** (optional)
   - File: Logo image
   - Format: PNG or JPG
   - Size: Max 2MB

---

## Step 5: Bank Account Information

Fill in your organization's bank details:

```
Bank Name: [Your bank]
Account Holder Name: Child of Hope Children's Foundation
Account Number: [Your account number]
Account Type: Savings / Checking
SWIFT Code: [Your bank's SWIFT code]
Currency: UGX
```

**Note**: This is where donations will be deposited

---

## Step 6: Set Security Credentials

### Create Strong Password
```
Minimum 8 characters
Include: UPPERCASE, lowercase, numbers, symbols (!@#$%)
Example: Ch1ldOfH0pe@2024!
```

### Enable Two-Factor Authentication
- Recommended for security
- Can use: Email or SMS verification
- Choose: SMS to your phone number

---

## Step 7: Review & Submit

1. **Review all information** - Verify every field is correct
2. **Accept Terms & Conditions** - Check the box
3. **Agree to Privacy Policy** - Check the box
4. **Submit Application** - Click "Register" or "Submit"

You should see: **"Registration submitted successfully"**

---

## Step 8: KYC Verification (2-5 business days)

After submission:

1. **Pesapal reviews documents** (2-5 business days)
2. **You receive email** with status update:
   - ✅ **Approved**: Proceed to Step 9
   - ⚠️ **Pending**: Additional documents needed
   - ❌ **Rejected**: Resubmit with corrections

**During this time**: Keep an eye on your email

### If Rejected:
- Pesapal will specify what needs correcting
- Resubmit corrected documents
- Process repeats

### If Approved:
- ✅ Move to Step 9
- ✅ Get production API keys

---

## Step 9: Access Production Dashboard

Once approved, you can:

1. **Log in to Pesapal Dashboard**
   - URL: https://www.pesapal.com/login
   - Username: Your email
   - Password: Your password

2. **Navigate to Settings > API Keys**
   - Find "Consumer Key"
   - Find "Consumer Secret"
   - Copy both values

---

## Step 10: Get Your API Keys

### Location in Dashboard:
```
Menu → Settings → API Integration → API Keys
```

You'll find:
- **Consumer Key**: Long string like `EnOIU7iJ1EV/ilL7YHvFi3f8Vu/JVAMq`
- **Consumer Secret**: String like `xIGCZxylKTXtiqLCNX2ahy8w9Yg=`

### Store These Securely:
```
Write them down in:
- Password manager (1Password, LastPass, etc.)
- Secure document (encrypted)
NOT in plain text or email
```

---

## Step 11: Update Your Website Configuration

### Update .env File:

1. Open `/Volumes/Moshe Works/MY WEBSITES/free-nonprofit-website-template/.env`

2. Replace with your production keys:
```env
PESAPAL_SANDBOX=false
PESAPAL_CONSUMER_KEY=your_production_consumer_key_here
PESAPAL_CONSUMER_SECRET=your_production_consumer_secret_here
ADMIN_PASSWORD=your_secure_admin_password
```

3. Save file
4. Set permissions: `chmod 600 .env`

---

## Step 12: Configure Callback URL

In Pesapal Dashboard:

1. Go to Settings → API Integration
2. Find "Callback URL" or "Webhook URL"
3. Enter:
```
https://your-website.com/pesapal_callback.php
```

Replace `your-website.com` with your actual domain

4. Save

---

## Step 13: Test Donations

1. **Visit your website** → Donate page
2. **Enter test donor info**:
   - Name: Test Donor
   - Email: test@example.com
   - Amount: 1,000 UGX (small test amount)

3. **Click Donate**
4. **Complete payment** on Pesapal
5. **Verify in admin dashboard**:
   - Check: http://your-website.com/pesapal_admin.php
   - Login with your admin password
   - Verify transaction appears

---

## ✅ Timeline

| Step | Duration | Status |
|------|----------|--------|
| Registration | 10-15 min | **Do Now** |
| Document Upload | 5-10 min | **Do Now** |
| KYC Review | 2-5 days | **Wait** |
| Configuration | 5 min | **After Approval** |
| Testing | 10 min | **After Approval** |
| **Total** | **~5 days** | — |

---

## 🆘 Troubleshooting

### "Registration Failed"
- ✅ Check all fields filled correctly
- ✅ Verify email address is correct
- ✅ Ensure password meets requirements
- ✅ Clear browser cache and retry

### "Document Rejected"
- ✅ Check file size (max 5MB)
- ✅ Verify file format (PDF or JPG)
- ✅ Ensure document is readable/clear
- ✅ Check organization name matches certificate

### "Still Pending After 5 Days"
- ✅ Check email for updates
- ✅ Log in to dashboard to see status
- ✅ Contact Pesapal support: support@pesapal.com
- ✅ Include your application reference number

### "Can't Find API Keys"
- ✅ Make sure you're in Production account (not sandbox)
- ✅ Check Dashboard → Settings → API Integration
- ✅ Verify account is fully approved
- ✅ Try logging out and back in

---

## 📞 Support Contacts

### Pesapal Support
- **Email**: support@pesapal.com
- **Website**: https://www.pesapal.com/support
- **Chat**: Available in Pesapal dashboard

### Your Website Support
- **Admin Dashboard**: http://your-website.com/pesapal_admin.php
- **Contact Us**: childofhopechildrensfoundation@gmail.com

---

## 🔒 Security Reminders

1. **NEVER share your API credentials**
2. **NEVER commit .env file to GitHub**
3. **Use strong admin password** (minimum 12 characters)
4. **Enable 2FA on Pesapal account**
5. **Use HTTPS only** for your website
6. **Back up .env file** in secure location

---

## ✨ After Everything is Set Up

Your website will be able to:
- ✅ Accept donations in UGX
- ✅ Process payments through Pesapal
- ✅ Send automatic receipts to donors
- ✅ Track all transactions
- ✅ View analytics in admin dashboard
- ✅ Export transaction data

---

## 📋 Checklist

### Before Registration:
- [ ] All documents scanned and ready
- [ ] Bank account details available
- [ ] Director ID information ready
- [ ] Organization details confirmed
- [ ] Strong password created
- [ ] Phone number for 2FA

### After Registration:
- [ ] Confirmation email received
- [ ] Check email for status updates
- [ ] Approval received (2-5 days)
- [ ] API keys obtained
- [ ] .env file updated
- [ ] Callback URL configured
- [ ] Test donation successful
- [ ] Admin can see transaction
- [ ] Receipt email received

### Production Ready:
- [ ] HTTPS enabled on website
- [ ] .env file secured (chmod 600)
- [ ] Admin password changed
- [ ] Backup of .env created
- [ ] 2FA enabled on Pesapal
- [ ] Support contacts saved

---

## 🎉 You're Ready!

Once approved, your nonprofit donation system will be live and ready to accept real UGX donations! 

**Questions?** 
- Review PESAPAL_DEPLOYMENT_GUIDE.md for production setup
- Check PESAPAL_README.md for quick reference
- See FINAL_TEST_REPORT.md for system status

Good luck! 🚀
