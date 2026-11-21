# Nakasero Market — Seller–Buyer Digital Marketplace

[![Tests](https://github.com/Julius-Mukubya/nakasero-market/actions/workflows/tests.yml/badge.svg)](https://github.com/Julius-Mukubya/nakasero-market/actions/workflows/tests.yml)
[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20.svg?logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4.svg?logo=php)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1.svg?logo=mysql&logoColor=white)](https://mysql.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.4-38B2AC.svg?logo=tailwind-css)](https://tailwindcss.com)

A web platform built with Laravel 12 to directly connect the traders and stallholders of **Nakasero Market in Kampala, Uganda** with wholesale buyers, retailers, restaurants, and households. It eliminates exploitative middlemen, offers transparent produce pricing in UGX, and provides digital stall management.

---

## 🌟 Key Features

### 🥬 1. Produce Catalog & 9 Core Categories
* **9 Visual Produce Categories** with custom circular badges and authentic produce imagery:
  * *Vegetables*, *Fruits*, *Grains*, *Tubers & Roots*, *Herbs & Spices*, *Poultry & Eggs*, *Fish & Seafood*, *Legumes & Pulses*, *Dairy & Honey*.
* Full-text search and multi-attribute filtering by category, market stall location, price range (UGX), and freshness.
* Unit pricing transparency (e.g. `UGX 3,500 / kg` or `UGX 25,000 / bunch`).

### ⚖️ 2. Interactive Produce Price Comparison
* **Dual-Mode Comparison Tool**:
  * **Browse Page Selection**: Checkbox tick up to 3 produce items from the listings page to pop up an interactive sticky compare bar.
  * **Direct Comparison Hub (`/compare`)**: Open `/compare` anytime to browse by category and select produce items side by side.
* **Side-by-Side Comparison Matrix**: Compare rates, lowest price indicators, available quantities, seller names, verified stall numbers, and location.

### 🏪 3. Responsive Dashboard with Role-Tailored Sidebar
* Built with `<x-layouts.dashboard>`:
  * **User Identity Card**: Displays initials avatar, user name, role tag (`✓ Verified Seller`, `⏳ Pending Seller`, `🛒 Produce Buyer`, `👑 Market Admin`), and stall info.
  * **Seller Navigation**: Dashboard overview, Produce Listings, + New Listing, Sales & Orders (with pending badge count), Messages (with unread badge count), Stall Profile.
  * **Buyer Navigation**: Dashboard overview, Browse Produce, Compare Prices, My Orders, Messages, Account Settings.
  * **Responsive**: Sticky desktop sidebar (`lg:w-64`) and touch-friendly horizontal scrollbar on mobile devices.

### 📞 4. Instant Seller Contacts for Logged-In Users
* Once logged in, buyers can immediately see the seller's direct phone number (with one-tap `tel:` calling) and email address (`mailto:`) directly on the listing page.
* Guests see an authentication prompt encouraging registration.

### 📝 5. Direct Order Requests & In-App Messaging
* **Dynamic Order Calculation**: Real-time quantity-to-total math powered by Alpine.js (`Quantity × Unit Price`).
* Built-in direct messaging between buyers and stallholders to confirm harvest freshness, bulk orders, and pickup times.
* Clear status transitions: `Requested` &rarr; `Accepted` / `Declined` &rarr; `Completed` / `Cancelled`.

### 🛡️ 6. Market Association Administration
* Verification badge system to certify genuine Nakasero Market stallholders.
* Listing moderation, category management, and user account status controls.

### 🌐 7. Universal 4-Column Footer
* Unified across all public, dashboard, and admin layouts featuring marketplace links, account shortcuts, and official Nakasero Market office hours and Kampala contact information.

---

## 🛠️ Tech Stack

* **Backend**: Laravel 12 (PHP 8.2+)
* **Database**: MySQL 8.0 (with SQLite support for in-memory testing)
* **Frontend**: Blade Components, Tailwind CSS, Alpine.js, Vite
* **CI/CD**: GitHub Actions automated MySQL test pipeline

---

## 🚀 Quickstart & Local Setup

### Prerequisites
* **PHP**: `>= 8.2` with extensions (`pdo_mysql`, `mbstring`, `gd`, `xml`, `curl`, `zip`)
* **Composer**: `>= 2.x`
* **Node.js**: `>= 18.x` & **npm**
* **MySQL**: `>= 8.0` (e.g. via XAMPP)

### Installation Steps

1. **Clone the repository:**
   ```bash
   git clone https://github.com/Julius-Mukubya/nakasero-market.git
   cd nakasero-market
   ```

2. **Install PHP and Node dependencies:**
   ```bash
   composer install
   npm install
   ```

3. **Configure the Environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Set up the Database:**
   Ensure MySQL is running (e.g. via XAMPP on port 3306), create a database named `nakasero_market`, and verify your `.env` configuration:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nakasero_market
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Run Migrations & Seeders:**
   ```bash
   php artisan migrate --seed
   ```
   *Seeds all 9 categories, 18 demo listings, and 4 test accounts.*

6. **Build Frontend Assets:**
   ```bash
   npm run build
   # or for development with hot reload:
   npm run dev
   ```

7. **Start the Development Server:**
   ```bash
   php artisan serve --port=8000
   ```
   Open `http://127.0.0.1:8000` in your browser.

---

## 🔑 Pre-Configured Test Accounts

All demo accounts share the password: **`password`**

| Role | Name | Email | Password | Dashboard & Access |
| :--- | :--- | :--- | :--- | :--- |
| **Market Admin** | Market Admin | `admin@nakasero.test` | `password` | `/admin/overview` &bull; Verify sellers, moderate produce, manage catalog |
| **Verified Seller** | DEMO Seller (Sarah) | `demo-farmer@nakasero.test` | `password` | `/farmer/dashboard` &bull; Stall *Shed 3, Stall 12* &bull; Active listings, stock, sales |
| **Pending Seller** | DEMO Seller (pending) | `demo-farmer2@nakasero.test` | `password` | `/farmer/dashboard` &bull; Stall *Shed 7, Stall 4* &bull; Awaiting admin verification badge |
| **Produce Buyer** | DEMO Buyer (James) | `demo-buyer@nakasero.test` | `password` | `/buyer/dashboard` &bull; Browse produce, compare prices, order placement |

---

## 🧪 Testing & CI/CD

### Running Tests Locally
The test suite contains **62 automated tests with 180 assertions** covering authentication, seller verification, listing CRUD, order lifecycles, and comparison logic:

```bash
php artisan test
```

### GitHub Actions CI
Every push or pull request to the `main` branch automatically triggers [.github/workflows/tests.yml](.github/workflows/tests.yml) which:
* Boots up a dedicated **MySQL 8.0** service container.
* Sets up PHP 8.3 & Node 22.
* Compiles Vite assets with `npm ci && npm run build`.
* Runs `php artisan test` against the MySQL test database.

---

## 📄 License
Open-sourced software licensed under the [MIT License](LICENSE).
