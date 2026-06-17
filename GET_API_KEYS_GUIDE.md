# Getting Your Pesapal API Keys - Manual Steps

**Account**: childofhopechildrensfoundation@gmail.com  
**Status**: ✅ Successfully logged into Pesapal

---

## How to Find Your API Keys

### Method 1: Via Merchant Dashboard 2.0 (Recommended)

1. **Log in to Pesapal**
   - URL: https://myaccount.pesapal.com/
   - Email: childofhopechildrensfoundation@gmail.com
   - Click "Merchant Dashboard 2.0"

2. **Navigate to Settings**
   - Once in dashboard, look for menu icon (☰) top-left
   - Click **Settings** or **Configuration**

3. **Find API Integration**
   - Look for **API** or **Integration** section
   - Click **API Keys** or **API Integration**

4. **Copy Your Keys**
   - You'll see two keys:
     - **Consumer Key** (long string starting with letters/numbers)
     - **Consumer Secret** (shorter string ending with =)
   - Copy both and save them safely

---

### Method 2: Via Legacy Dashboard (Alternative)

If the new dashboard doesn't load:

1. **Try Legacy Dashboard**
   - URL: https://www.pesapal.com/login
   - Log in with same credentials

2. **Look for "API Key"**
   - Once logged in, find **Settings** or **My Account**
   - Look for **API Credentials** or **API Integration**

3. **Get Your Keys**
   - Consumer Key
   - Consumer Secret

---

## 📋 What You're Looking For

### Consumer Key
```
Example format: EnOIU7iJ1EV/ilL7YHvFi3f8Vu/JVAMq
Length: ~30+ characters
Contains: Letters, numbers, slashes, underscores
```

### Consumer Secret
```
Example format: xIGCZxylKTXtiqLCNX2ahy8w9Yg=
Length: ~20+ characters
Ends with: = (equals sign)
Contains: Letters, numbers, equals sign
```

---

## 🔐 Once You Have the Keys

1. **Open Your .env File**
   ```
   /Volumes/Moshe Works/MY WEBSITES/free-nonprofit-website-template/.env
   ```

2. **Update the Keys**
   ```env
   PESAPAL_SANDBOX=false
   PESAPAL_CONSUMER_KEY=your_actual_consumer_key_here
   PESAPAL_CONSUMER_SECRET=your_actual_consumer_secret_here
   ADMIN_PASSWORD=changeme123
   ```

3. **Save the File**

4. **Upload to Production**
   - Move all Pesapal files to your web server
   - Create .env on server with same credentials
   - Set permissions: `chmod 600 .env`

---

## 🆘 Troubleshooting

### "Can't Find API Settings"
- ✅ Make sure you're viewing **Production** account, not Sandbox
- ✅ Check for a toggle/switch for Production vs Sandbox
- ✅ Try the Settings menu again

### "Keys Look Different"
- ✅ That's normal - each Pesapal account has unique keys
- ✅ Just copy whatever you see exactly as shown
- ✅ Don't modify or format them

### "Still Can't Access Dashboard"
- ✅ Try clearing browser cache
- ✅ Try a different browser
- ✅ Contact Pesapal support: support@pesapal.com

---

## ✅ Next Steps After Getting Keys

1. **Update .env** file in your project
2. **Change `PESAPAL_SANDBOX=true` to `false`**
3. **Set up HTTPS** on your website
4. **Deploy to production** server
5. **Update callback URL** in Pesapal:
   - Settings → API Integration
   - Callback URL: `https://your-domain.com/pesapal_callback.php`
6. **Test with small donation** (1,000 UGX)
7. **Verify transaction** appears in admin dashboard

---

## 📞 Need More Help?

- **Pesapal Support**: support@pesapal.com
- **Check Guides**:
  - PESAPAL_DEPLOYMENT_GUIDE.md (production setup)
  - PESAPAL_README.md (quick reference)

---

**Once you have your API keys, reply with them and I'll update your configuration!**
