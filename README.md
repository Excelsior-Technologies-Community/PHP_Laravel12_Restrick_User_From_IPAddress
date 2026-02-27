#  PHP_Laravel12_Restrict_User_From_IPAddress

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel">
  <img src="https://img.shields.io/badge/Security-IP%20Restriction-success?style=for-the-badge">
  <img src="https://img.shields.io/badge/Middleware-Custom-blue?style=for-the-badge">
</p>

---

##  Overview  

Laravel 12 allows you to secure your application using **Middleware**.  
In this tutorial, we will **restrict or block users based on their IP address**.

###  Key Highlights
- Block specific IP addresses  
- Return **403 Forbidden** response for blocked IPs  
- Fully compatible with **Laravel 12** (no `Kernel.php`)  
- Easy to apply on routes or route groups  

---

##  Features  

-  Restrict access by IP address  
-  Uses **Custom Middleware**  
-  Automatically blocks restricted users  
-  Clean & maintainable code  
-  Laravel 12 middleware registration support  

---

##  Use Cases  

- Admin panel access only from company IPs  
- Blocking suspicious or malicious IP addresses  
- Restricting staging or internal tools  
- Extra security layer on sensitive routes  

---

##  What You Will Learn  

By following this guide, you will learn how to:

- ✔ Create a **custom middleware** in Laravel 12  
- ✔ Block users based on IP address  
- ✔ Register middleware in `bootstrap/app.php` (Laravel 12 way)  
- ✔ Apply middleware to route groups  
- ✔ Return **403 Forbidden** response for blocked users  

---

##  Security Level  

| Feature                  | Status |
|--------------------------|--------|
| IP Whitelist / Blacklist | ✅ Supported |
| Route-level protection   | ✅ Supported |
| Laravel 12 Compatible    | ✅ Yes |
| Admin-ready              | ✅ Yes |

---

##  Folder Structure  

```
app/
├── Http/
│   └── Middleware/
│       └── BlockIpMiddleware.php

routes/
└── web.php

bootstrap/
└── app.php

.env
README.md
```

---

##  Step 1 — Install Laravel 12  

Create a fresh Laravel 12 project:

```bash
composer create-project laravel/laravel laravel
```

Move into project directory:

```bash
cd laravel
```

---

##  Step 2 — Configure Environment File (.env)  

Open `.env` file and update database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

---

##  Step 3 — Create Block IP Middleware  

Run the artisan command:

```bash
php artisan make:middleware BlockIpMiddleware
```

This will create:

```
app/Http/Middleware/BlockIpMiddleware.php
```

---

##  Step 4 — Write Block IP Logic  

 **app/Http/Middleware/BlockIpMiddleware.php**

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockIpMiddleware
{
    /**
     * List of blocked IP addresses
     */
    public $blockIps = [
        'whitelist-ip-1',
        'whitelist-ip-2',
        '127.0.0.1', // Localhost example
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user's IP is in blocked list
        if (in_array($request->ip(), $this->blockIps)) {
            abort(403, 'You are restricted to access the site.');
        }

        return $next($request);
    }
}
```

---

##  Step 5 — Register Middleware in Laravel 12  

⚠ **Laravel 12 does NOT use `Kernel.php`**  
Middleware is registered inside **`bootstrap/app.php`**

 **bootstrap/app.php**

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware): void {

        // Register custom middleware alias
        $middleware->alias([
            'blockIP' => \App\Http\Middleware\BlockIpMiddleware::class,
        ]);

    })

    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })

    ->create();
```

---

##  Step 6 — Apply Middleware to Routes  

 **routes/web.php**

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RSSFeedController;

/*
|--------------------------------------------------------------------------
| Routes protected by Block IP Middleware
|--------------------------------------------------------------------------
*/
Route::middleware(['blockIP'])->group(function () {

    Route::resource('users', UserController::class);
    Route::resource('rss', RSSFeedController::class);

});
```

---

##  Step 7 — Run Laravel Application  

Start the development server:

```bash
php artisan serve
```

Application will run at:

```
http://localhost:8000
```

---

##  Step 8 — Test IP Restriction  

Visit:

```
http://localhost:8000/users
```

###  If your IP is blocked  
You will see:

```
403 | You are restricted to access the site.
```
<img width="1805" height="970" alt="Screenshot 2025-12-15 103539" src="https://github.com/user-attachments/assets/33f263ac-60cc-4584-9665-194bd08f487d" />



###  If your IP is allowed  
The page will load normally.

---

##  Conclusion  

✔ Middleware checks IP before request  
✔ Blocked IPs receive 403 error  
✔ Fully compatible with Laravel 12  
✔ Can be applied to specific routes or entire app  

---

