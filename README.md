# MiladSarli Cart System

یک سیستم سبد خرید انعطاف پذیر برای اپلیکیشن های لاراول

## نصب

برای نصب این پکیج با استفاده از Composer دستور زیر را اجرا کنید:

```bash
composer require miladsarli/cartsystem
```

## پیکربندی

پس از نصب، دستور زیر را اجرا کنید تا فرآیند نصب به صورت خودکار انجام شود:

```bash
php artisan cart:install
```

این دستور به صورت خودکار:
1. فایل‌های کانفیگ را منتشر می‌کند
2. مایگریشن‌ها را کپی می‌کند
3. مدل‌ها را در پوشه `App\Models\Cart` کپی می‌کند
4. کنترلرها را در پوشه `App\Http\Controllers\Cart` کپی می‌کند
5. مسیرها را در پوشه `routes/cart` کپی می‌کند
6. مسیرها را به صورت خودکار در `routes/api.php` اضافه می‌کند

### نصب دستی فایل‌ها

اگر می‌خواهید فقط بخشی از فایل‌ها را نصب کنید، می‌توانید از دستورات زیر استفاده کنید:

```bash
# برای نصب فایل کانفیگ
php artisan vendor:publish --tag=cart-config

# برای نصب مایگریشن‌ها
php artisan vendor:publish --tag=cart-migrations

# برای نصب مدل‌ها
php artisan vendor:publish --tag=cart-models

# برای نصب کنترلرها
php artisan vendor:publish --tag=cart-controllers

# برای نصب مسیرها
php artisan vendor:publish --tag=cart-routes
```

### تنظیم مسیرها به صورت دستی

اگر مسیرها به صورت خودکار اضافه نشده‌اند، این خط را به فایل `routes/api.php` اضافه کنید:

```php
require base_path('routes/cart/api.php');
```

## تنظیمات .env

تنظیمات زیر را می‌توانید در فایل .env خود تعریف کنید:

```env
CART_ROUTE_PREFIX=api/v1
CART_ITEM_EXPIRATION=24
CART_MAX_ITEMS=50
CART_EVENTS_ENABLED=true
CART_PRICE_PRECISION=2
CART_TAX_ENABLED=true
CART_DEFAULT_TAX_RATE=0
CART_MULTI_TENANCY_ENABLED=false
```

## نحوه استفاده

### افزودن محصول به سبد خرید
```php
POST /api/v1/cart
{
    "product_id": 1,
    "variant_id": null, // اختیاری
    "quantity": 1,
    "tenant_id": null // اختیاری - برای سیستم‌های چند فروشگاهی
}
```

### دریافت لیست سبد خرید
```php
GET /api/v1/cart
```

### بروزرسانی تعداد محصول
```php
PUT /api/v1/cart/{cart_id}
{
    "quantity": 2
}
```

### حذف محصول از سبد خرید
```php
DELETE /api/v1/cart/{cart_id}
```

### خالی کردن سبد خرید
```php
DELETE /api/v1/cart
```

### ثبت نهایی سفارش
```php
POST /api/v1/cart/checkout
{
    "address_id": 1
}
```

## امکانات

- مدیریت سبد خرید با پشتیبانی از تنوع محصولات
- محاسبه خودکار قیمت با در نظر گرفتن تخفیف‌ها
- پشتیبانی اختیاری از چند فروشگاه (Multi-tenancy)
- مدیریت تراکنش‌ها
- سیستم آدرس دهی
- پشتیبانی از Soft Delete
- قابلیت تنظیم مدت زمان انقضای سبد خرید
- محدودیت تعداد آیتم در سبد خرید
- پشتیبانی از محاسبه مالیات
- امکان غیرفعال کردن ویژگی‌های اضافی

## ساختار فایل‌ها

پس از نصب، فایل‌های پکیج در مسیرهای زیر قرار می‌گیرند:

- کانفیگ: `config/cart.php`
- مایگریشن‌ها: `database/migrations/`
- مدل‌ها: `app/Models/Cart/`
- کنترلرها: `app/Http/Controllers/Cart/`
- مسیرها: `routes/cart/api.php`

## لایسنس

این پکیج تحت لایسنس MIT منتشر شده است.
