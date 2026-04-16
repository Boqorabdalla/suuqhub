# Atlas Business Directory - Pre-Publish Checklist

## ✅ What's Already Done

### Mobile Responsiveness
- Bootstrap CSS framework included
- Responsive CSS file (`responsive.css`) included
- Mobile meta tags configured in `layouts/seo.blade.php`
- Touch-friendly design elements

### PWA Implementation
- `manifest.json` created in public folder
- `sw.js` (Service Worker) created
- Service worker registration added to frontend
- PWA meta tags added
- Offline page created
- SVG icons created (192x192 and 512x512)

---

## 📋 Pre-Publish Checklist

### 1. Environment Configuration
```bash
# Local Development (.env)
APP_ENV=local
APP_DEBUG=true

# Production (.env) - CHANGE THESE
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
```

### 2. Generate Application Key
```bash
php artisan key:generate
```

### 3. Clear All Caches
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
composer dump-autoload
```

### 4. Optimize for Production
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🚀 Publishing to cPanel Steps

### Step 1: Backup (Important!)
```bash
# On cPanel (via SSH or Terminal)
mysqldump -u database_username -p database_name > backup_$(date +%Y%m%d).sql
```

### Step 2: Export Local Database
```bash
mysqldump -u root -p atlas_database > local_backup.sql
```

### Step 3: Upload Files
- Upload all files from `/atlas/` folder to `public_html/` or your subdomain folder
- **Important:** Don't upload the `vendor` folder if using Composer on server

### Step 4: Upload Database
```bash
# Create new database on cPanel
# Import local database
mysql -u cpanel_user -p new_database < local_backup.sql
```

### Step 5: Update .env File
```env
APP_NAME="Atlas"
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_KEY
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_cpanel_database
DB_USERNAME=your_cpanel_username
DB_PASSWORD=your_database_password

CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
```

### Step 6: Set Permissions
```bash
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chmod -R 775 public/uploads/
```

### Step 7: Install Composer Dependencies
```bash
composer install --optimize-autoloader --no-dev
```

### Step 8: Run Migrations & Seeders
```bash
php artisan migrate
php artisan db:seed
```

### Step 9: Clear Caches Again
```bash
php artisan config:cache
php artisan cache:clear
```

---

## 🔧 Important Configuration

### 1. Update URLs in Database
After importing database, run these SQL queries:
```sql
UPDATE settings SET value='https://yourdomain.com' WHERE key='system_url';
UPDATE settings SET value='https://yourdomain.com' WHERE key='frontend_url';
```

### 2. Create Storage Link
```bash
php artisan storage:link
```

### 3. Schedule Cron Jobs (Optional)
Add to crontab:
```bash
* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1
```

---

## 📱 PWA Icon Requirements

The current SVG icons are placeholders. For production, replace with:

### Required Icons:
- `public/image/icon-192.png` (192x192px PNG)
- `public/image/icon-512.png` (512x512px PNG)

### Icon Guidelines:
- Use your logo with transparent background
- Follow Apple's App Icon guidelines
- Use maskable icon format for Android

---

## ⚠️ Common Issues & Solutions

### Issue 1: White Screen After Upload
**Solution:**
```bash
php artisan config:cache
php artisan view:clear
```

### Issue 2: Database Connection Error
**Solution:** Check `.env` database credentials match cPanel settings

### Issue 3: Permission Denied Errors
**Solution:**
```bash
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

### Issue 4: Assets Not Loading
**Solution:**
```bash
php artisan asset:clear
php artisan config:cache
```

### Issue 5: HTTPS Mixed Content Warning
**Solution:** Update `.env`:
```env
FORCE_HTTPS=true
```

---

## 📋 Final Pre-Launch Checklist

- [ ] APP_DEBUG=false in .env
- [ ] APP_ENV=production in .env
- [ ] HTTPS enabled on domain
- [ ] SSL certificate installed
- [ ] Database credentials updated
- [ ] All caches cleared
- [ ] Permissions set correctly
- [ ] Storage link created
- [ ] PWA icons replaced with proper PNG versions
- [ ] Test on mobile device
- [ ] Test checkout flow
- [ ] Test order creation
- [ ] Test email notifications
- [ ] Check error logs

---

## 📞 Post-Launch Recommendations

1. **Monitor Error Logs**
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Set Up Analytics**
   - Google Analytics
   - Search Console

3. **Enable Backups**
   - Set up automated database backups
   - Backup storage/uploads folder regularly

4. **Security Hardening**
   - Enable two-factor authentication
   - Use strong passwords
   - Regular security updates

---

## 📞 Support

For issues, check:
1. Apache/Nginx error logs
2. Laravel logs: `storage/logs/laravel.log`
3. cPanel error log
