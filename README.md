# City Pharmacy — Full Pharmacy Management System

A complete Pharmacy POS + Inventory + E-commerce frontend built with **Laravel 12, MySQL, Bootstrap 5, jQuery**, and modern plugins (AOS, Swiper, Isotope, Venobox, Chart.js).

## ✅ What's included

**Backend**
- Role & Permission system (Admin, Manager, Pharmacist, Salesman, Accountant) with a permission matrix
- Product / Medicine management with **generic name**, brand, category, unit, dosage form, expiry tracking
- Purchase module (stock IN) and POS Sales module (stock OUT) — **stock auto-updates on every purchase/sale**
- Stock movement ledger (full audit trail of every stock change)
- Expense tracking with categories
- Dashboard with Chart.js graphs: sales trend, category stock split, sales-vs-purchase, top products
- Reports: Daily / Weekly / Monthly / Yearly Sales, Purchase, Expense, **Profit & Loss**, Inventory
- Site Settings + SEO (meta title/description/keywords, OG image, Google Analytics)
- Activity Log (who did what, when)
- Backup module (via `spatie/laravel-backup`, scheduled daily at 2 AM)
- Idempotent seeders (see note below)

**Frontend**
- Hero slider (Swiper.js), parallax counter section, AOS scroll animations
- Isotope category grid, Venobox image/quick-view modal, accordion FAQ
- Dark theme toggle, language switch (en/bn), preloader, back-to-top button
- Shop page with filters (category, generic name, price sort) + product detail page
- Full footer with quick links, categories, social links, newsletter box

## 🌱 About the seeder behavior (as requested)

All seeders use `firstOrCreate()` / `updateOrCreate()`:
- If a record **already exists locally** (you added it through the admin panel), the seeder leaves it untouched.
- If it **doesn't exist yet**, the seeder creates it from the defaults.

So it's always safe to re-run `php artisan db:seed` — your real data is never duplicated or overwritten, and only the missing pieces get filled in from the seeder. (`জেনো লোকাল ডেটা থাকুক বা না থাকুক, সিডার থেকেই যা দরকার আসবে — থাকলে ওভাররাইট হবে না।`)

## ⚙️ Setup (local machine)

```bash
# 1. Install dependencies
composer install

# 2. Environment
cp .env.example .env
php artisan key:generate

# 3. Configure MySQL in .env (DB_DATABASE, DB_USERNAME, DB_PASSWORD)
#    then create the database, e.g.:
mysql -u root -p -e "CREATE DATABASE pharmacy_db"

# 4. Migrate + seed
php artisan migrate --seed

# 5. Storage link (for product images)
php artisan storage:link

# 6. Run
php artisan serve
```

Visit `http://localhost:8000` for the storefront and `http://localhost:8000/login` for the admin panel.

**Demo logins (from `UserSeeder`):**
| Role      | Email                  | Password |
|-----------|------------------------|----------|
| Admin     | admin@pharmacy.test    | password |
| Manager   | manager@pharmacy.test  | password |
| Salesman  | sales@pharmacy.test    | password |

## 🗂 Project structure notes

- `app/Models/Product.php` → `adjustStock()` is the single place stock changes happen; every purchase/sale/adjustment goes through it and writes a `stock_movements` row.
- `app/Http/Controllers/Admin/SaleController.php` → POS checkout: locks product rows (`lockForUpdate`), validates stock, computes profit per line, deducts stock.
- `app/Http/Controllers/Admin/PurchaseController.php` → stores purchase items, updates product cost/sale price + expiry, increases stock.
- `app/Http/Middleware/CheckPermission.php` / `CheckRole.php` → route-level guards, used as `->middleware('permission:product.view')` or `->middleware('role:admin')`.
- `database/seeders/DatabaseSeeder.php` → the master seeder; runs role/permission, users, catalog, products, settings, sliders, and (only if no real transactions exist yet) demo purchase/sale data for the dashboard charts.

## 🆕 Just added (this update)

**Multi-branch**
- `branches` table + `branch_id` on users, purchases, sales, expenses, stock movements.
- Admin topbar branch switcher (for Admin/Manager); each staff member otherwise operates on their assigned branch (`users.branch_id`).
- POS, Purchases, manual stock adjustments, and online-order fulfilment are all branch-scoped.

**Batch & expiry tracking (FEFO)**
- New `product_batches` table: every unit of stock lives inside a batch (batch no, quantity, cost, sale price, mfg/expiry dates, branch).
- `Product::receiveBatch()` (purchases / stock-in) and `Product::issueStock()` (sales / stock-out) are the only ways stock changes — the latter consumes **First-Expiry-First-Out**, and records exactly which batches a sale drew from (`sale_items.batch_allocations`), so cancelling a sale restores the exact batches.
- `products.stock_qty` and `products.expiry_date` are now **cached aggregates** recalculated from batches (`Product::recalcStock()`) — don't edit them directly.
- Admin → Batches: full batch ledger + filters; Admin → Expiry Alerts: expiring-soon / already-expired lists with a one-click "email admins" button, plus a daily scheduled digest (`php artisan pharmacy:expiry-alerts`, see `routes/console.php`).

**Barcode & QR code**
- `picqer/php-barcode-generator` renders CODE-128 barcodes as inline SVG (`App\Services\BarcodeService::svg()`), embedded in PDF invoices and printable labels.
- `simplesoftwareio/simple-qrcode` renders QR codes (used on printable labels, linking to the product's storefront page).
- Admin → Barcode/QR Labels: pick products + quantity, get a printable label sheet (barcode + QR + price).
- The POS search box already doubles as a barcode-scanner input (type/scan the code, press Enter).

**PDF invoices**
- `barryvdh/laravel-dompdf` — "Download PDF" button on both Sale and Purchase invoice pages (`admin.sales.pdf`, `admin.purchases.pdf`), styled A5 invoices with an embedded barcode of the invoice number.

**SMS / Email alerts**
- `App\Services\SmsService` — generic HTTP SMS gateway wrapper (works with most Bangladeshi providers' simple REST APIs); defaults to a `log` driver so nothing breaks in local dev. Configure via `SMS_DRIVER` / `SMS_API_URL` / `SMS_API_KEY` / `SMS_SENDER_ID` in `.env`.
- `App\Notifications\Channels\SmsChannel` — custom Laravel notification channel.
- Notifications: `LowStockAlert` (mail, auto-fired the moment a sale drops a product to/below its alert quantity), `ExpiryAlert` (mail, manual + daily scheduled), `OrderConfirmation` (mail **and** SMS, fired when a storefront checkout completes).

**Online checkout**
- Guest cart (session-based, no account needed): `/cart`, add/update/remove.
- `/checkout` → creates a `Sale` with `channel = online`, `order_status` lifecycle (pending → processing → shipped → delivered), deducts stock via FEFO from the **main branch**, fires `OrderConfirmation`.
- `/track-order` — customer looks up their order by invoice number + phone.
- Admin → Online Orders: list/filter by status, update order status, download PDF invoice.

## ⚠️ Notes on this update

- `config/backup.php` here is a minimal stub — once you `composer install`, run `php artisan vendor:publish --tag=backup-config` to get Spatie's full default config and merge in the `name`/`source` overrides shown here.
- Online checkout ships as **guest checkout** (name + phone + address, no login) to keep scope manageable — a full customer-account system (order history, saved addresses) is a natural next step.
- Payment methods (`mobile_banking`, `card`) are recorded but not integrated with a real payment gateway (bKash/Nagad/SSLCommerz) — currently "cash on delivery" style. Say the word and I'll wire up a real gateway.
- `product_batches` is the single source of truth for stock; if you ever write to `products.stock_qty` directly (e.g. via a raw DB query), call `$product->recalcStock()` afterwards or it'll be overwritten on the next stock movement.

## 🔧 Next steps you may want to add later

- Online checkout / cart for the storefront (currently "contact to order")
- Email/SMS notifications for low stock or order confirmation
- PDF invoice export (`barryvdh/laravel-dompdf` is already in `composer.json`)
- Multi-branch / multi-warehouse stock
- Customer-facing order history & accounts

Since you said *"পরে অন্য কিছু লাগলে বলবো add করতে"* — just tell me what to add next and I'll extend this same codebase.
#   p h a m a c y  
 