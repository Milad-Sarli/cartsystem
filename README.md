# MiladSarli Cart System

یک سیستم سبد خرید انعطاف پذیر برای اپلیکیشن های لاراول

## نصب

برای نصب این پکیج با استفاده از Composer دستور زیر را اجرا کنید:

```bash
composer require miladsarli/cartsystem
```

## پیکربندی

پس از نصب، فایل های پیکربندی را با دستور زیر منتشر کنید:

```bash
php artisan vendor:publish --provider="MiladSarli\CartSystem\CartServiceProvider"
```

## نحوه استفاده

### افزودن محصول به سبد خرید
```php
POST /api/v1/cart
{
    "product_id": 1,
    "variant_id": null,
    "quantity": 1
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
- محاسبه خودکار قیمت با در نظر گرفتن تخفیف ها
- پشتیبانی از چند فروشگاه
- مدیریت تراکنش ها
- سیستم آدرس دهی
- پشتیبانی از Soft Delete

## لایسنس

این پکیج تحت لایسنس MIT منتشر شده است.
