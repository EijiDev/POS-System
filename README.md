# ☕ BrewPOS — Coffee Shop Point of Sale System

A full-featured POS system built with **Laravel 12** for coffee shops. Includes sales, inventory, expenses, customer loyalty, and analytics.

---

## Requirements

Before you start, make sure you have the following installed:

| Tool | Version | Download |
|------|---------|----------|
| XAMPP | 8.2+ | https://www.apachefriends.org |
| PHP | 8.2+ | Included with XAMPP |
| MySQL | 8.0+ | Included with XAMPP |
| Composer | Latest | https://getcomposer.org |
| Git | Latest | https://git-scm.com |

> **Note:** XAMPP already includes PHP and MySQL. Just make sure to use PHP 8.2 or higher.

---

## Setup Instructions

### 1. Start XAMPP

Open **XAMPP Control Panel** and start both:
- ✅ Apache
- ✅ MySQL

---

### 2. Clone the Project

Open your terminal or command prompt and navigate to the XAMPP `htdocs` folder:

```bash
cd C:\xampp\htdocs
```

Clone the repository:

```bash
git clone https://github.com/your-username/POS-System.git
cd POS-System
```

> Replace `your-username` with the actual GitHub username.

---

### 3. Install PHP Dependencies

```bash
composer install
```

---

### 4. Set Up Environment File

Copy the example environment file:

```bash
copy .env.example .env
```

Then generate the application key:

```bash
php artisan key:generate
```

---

### 5. Configure the Database

Open **phpMyAdmin** in your browser:
```
http://localhost/phpmyadmin
```

Create a new database:
- Click **New**
- Database name: `pos_system`
- Collation: `utf8mb4_unicode_ci`
- Click **Create**

Now open `.env` in a text editor and update the database settings:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pos_system
DB_USERNAME=root
DB_PASSWORD=
```

> If your MySQL has a password, enter it in `DB_PASSWORD`.

---

### 6. Run Migrations & Seed Demo Data

This will create all the tables and populate them with realistic demo data:

```bash
php artisan migrate --seed
```

This seeds:
- ✅ Admin user account
- ✅ 14 products across 4 categories (Coffee, Non-Coffee, Pastry, Snacks)
- ✅ ~2,000 orders spread over 60 days with realistic patterns
- ✅ 8 loyalty customers across Bronze / Silver / Gold tiers
- ✅ 3 months of expenses (Rent, Staff, Utilities, Stock, Maintenance)

---

### 7. Create Storage Symlink

This is required for product image uploads to work:

```bash
php artisan storage:link
```

---

### 8. Run the Application

```bash
php artisan serve
```

Open your browser and go to:
```
http://localhost:8000
```

---

## Login Credentials

| Field    | Value                  |
|----------|------------------------|
| Email    | admin@brewpos.com      |
| Password | admin123               |

---

## Features

| Page | Description |
|------|-------------|
| 🏠 Dashboard | Live stats, sales chart, top products, recent transactions |
| 🧾 Point of Sale | Product browser, order management, cash payment with change calculator |
| 📦 Products | Full product catalog with CRUD, image upload, stock tracking |
| 💸 Expenses | Track business costs by category with monthly summaries |
| 👥 Customers | Loyalty program — points, tiers (Bronze/Silver/Gold), visit tracking |
| 📊 Reports | Revenue analytics, popular items, category breakdown, Excel export |

---

## Resetting Demo Data

To wipe all data and re-seed fresh demo data at any time:

```bash
php artisan migrate:fresh --seed
```

---

## Troubleshooting

**`php` is not recognized**
> Add PHP to your system PATH. In XAMPP, PHP is at `C:\xampp\php`. Add this to your Environment Variables → System Variables → Path.

**`composer` is not recognized**
> Download and install Composer from https://getcomposer.org/download/ and restart your terminal.

**Database connection error**
> Make sure MySQL is running in XAMPP Control Panel and your `.env` DB credentials are correct.

**Page shows 404 after setup**
> Make sure Apache's `mod_rewrite` is enabled. In XAMPP, open `httpd.conf` and ensure `AllowOverride All` is set for your htdocs directory.

**Images not showing**
> Run `php artisan storage:link` if you haven't already.

---

## Tech Stack

- **Backend:** Laravel 12 (PHP 8.2)
- **Database:** MySQL 8
- **Frontend:** Vanilla JS, Chart.js
- **Excel Export:** Maatwebsite/Laravel-Excel
- **Fonts:** Plus Jakarta Sans (Google Fonts)
- **Server:** XAMPP (Apache + MySQL)

---

## Project Structure

```
POS-System/
├── app/
│   ├── Controllers/        # Auth, Dashboard, POS, Products, Expenses, Customers, Reports
│   ├── Exports/            # Excel export sheets (Summary, Orders, Popular Items, etc.)
│   └── Models/             # Order, OrderItem, Product, Expense, Customer
├── database/
│   ├── migrations/         # All table schemas
│   └── seeders/            # DatabaseSeeder with realistic demo data
├── public/
│   ├── css/                # Page stylesheets
│   └── js/                 # Page scripts
├── resources/views/
│   ├── layouts/            # app.blade.php base layout
│   ├── partials/           # sidebar.blade.php (reusable)
│   ├── auth/               # login.blade.php
│   ├── dashboard/          # index + partials
│   ├── pos/                # index + modals
│   ├── product/            # index + modals
│   ├── expenses/           # index
│   ├── customers/          # index
│   └── reports/            # index
└── routes/
    └── web.php             # All application routes
```

---

*Built with ❤️ and ☕*
