# پروژه فروشگاهی - REST API

## آدرس‌های مرتبط

- **گیت‌هاب پروژه:** [مشاهده در GitHub](https://github.com/B-Mohammad/online_shop_with_slim_framework)
- **مستندات Postman:** [مشاهده مستندات](https://documenter.getpostman.com/view/26425641/2sAYkDNgRY)

## نیازمندی‌ها

- **PHP** نسخه ۸.۲
- **MySQL**
- **پکیج‌های مورد نیاز:**
  - Slim
  - JWT
  - PSR
  - Valitron

## مراحل راه‌اندازی

1. ایجاد دیتابیس `online_shop` و لود کردن فایل `online_shop.sql`
2. استخراج فایل زیپ پروژه در مسیر `xampp/htdocs`
3. فعال‌سازی ویرچوال هاست:
   - ویرایش فایل `httpd.conf`
   - افزودن پیکربندی زیر در `httpd-vhosts.conf`:

```apache
<VirtualHost *:80>
    ServerName shop.localhost
    DocumentRoot "/opt/lampp/htdocs/slim/public"
    <Directory "/opt/lampp/htdocs/slim/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

4. ریست کردن سرویس Apache
5. دسترسی به پروژه از طریق `shop.localhost`

## ساختار پروژه

- **`public/`**: شامل `index.php` برای راه‌اندازی فریمورک Slim
- **`config/`**: شامل تنظیمات در `definition.php` و مسیرهای API در `routes.php`
- **`src/App/Controller`**: شامل کنترلرهای مربوط به احراز هویت، کالاها، سبد خرید و سفارشات
- **`src/App/Middleware`**: شامل مدیریت خطا و احراز هویت
- **`src/App/Repo`**: مدیریت ارتباط با دیتابیس
- **`src/App/Database.php`**: برقراری ارتباط با دیتابیس با استفاده از `PDO`

## نکات مهم

- **پکیج‌های مورد نیاز همراه پروژه هستند و نیازی به نصب مجدد نیست.**
- **رمز عبور کاربران `12345` است اما در دیتابیس هش شده ذخیره شده است.**
- **Dependency Injection برای مدیریت وابستگی‌ها استفاده شده است.**
