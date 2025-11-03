# Laravel eCommerce Test Project

> This project is a fully functional eCommerce platform built with Laravel, designed to provide a complete shopping experience for customers and robust management tools for administrators. The system supports user authentication, allowing customers to register, log in, reset passwords, and manage their profiles, while administrators access a separate Admin Dashboard to oversee products, categories, users, and orders.

Customers can browse products, apply filters by category or price, and view detailed product pages with multiple images, variants, and reviews. The platform includes a shopping cart and wishlist system, enabling users to add, update, or remove items. During checkout, users can provide address details, choose a payment method, and confirm orders. Each order automatically updates stock levels and notifies both the customer and admin via email.

The Admin Panel features sales analytics, order management, coupon creation, and product control with CRUD operations. Optional modules such as Coupons, Reviews & Ratings, and video reviews via WebRTC enhance engagement and marketing value. Security features include input validation, role-based authentication, and protection against common vulnerabilities.

Built with modern Laravel best practices, the project emphasizes clean architecture, scalability, and performance optimization using caching and queues. It can easily be extended with payment gateways like Stripe or Razorpay, API endpoints for mobile apps, and integrations for shipping or analytics. This project demonstrates a complete, production-ready eCommerce solution following industry standards.

---

## Table of Contents

1. [Project Overview](#project-overview)
2. [Features & Explanations](#features--explanations)

   * Authentication & User Management
   * Product & Category Management
   * Product Browsing & Search
   * Shopping Cart & Wishlist
   * Checkout & Orders
   * Order History & Admin Dashboard
   * Reviews & Ratings (optional)
   * Coupons & Discounts (optional)
   * Notifications & Emails
   * Deployment & Optimization
3. [Tech Stack](#tech-stack)
4. [Installation & Setup](#installation--setup)
5. [Database Schema (High-level)](#database-schema-high-level)
6. [Important Routes & API Endpoints (examples)](#important-routes--api-endpoints-examples)
7. [Admin Panel — Functionality & Notes](#admin-panel--functionality--notes)
8. [Checkout Flow & Stock Management](#checkout-flow--stock-management)
9. [Reviews, Ratings & WebRTC (optional)](#reviews-ratings--webrtc-optional)
10. [Coupon/Discount Rules (optional)](#coupondiscount-rules-optional)
11. [Notifications, Emails & Queues](#notifications-emails--queues)
12. [Deployment Tips & Optimization](#deployment-tips--optimization)
13. [Testing](#testing)
14. [Security Considerations](#security-considerations)
15. [Environment Variables](#environment-variables)
16. [Useful Artisan Commands](#useful-artisan-commands)
17. [How to extend / Next steps](#how-to-extend--next-steps)

---

## Features & Explanations

### 1. Authentication & User Management

**For Users**

* Register, Login, Forgot Password: Uses Laravel Breeze / Fortify / built-in auth scaffolding. Password resets via signed email token.
* Email Verification: Laravel's verification feature; users must verify before making purchases (configurable).
* Profile Management: Update profile fields (name, email, phone, address) and change password.

**For Admins**

* Separate Admin Login & Dashboard: Admin routes are protected by `auth` + `is_admin` middleware or separate guard.
* Manage Users: Admin can view users, block/unblock accounts, view individual user's orders, and assign roles.

**Implementation notes**

* Use policies and gates for authorization (`UserPolicy`) and middleware `IsAdmin`.
* Store roles in `role` column or a `roles` table for flexible RBAC.

---

### 2. Product & Category Management

**Admin CRUD for Products**

* Product fields: `title`, `slug`, `description`, `price`, `discount`, `sku`, `stock_quantity`, `status`.
* Multiple images: Stored via Laravel Filesystem (S3/local). Images saved in `product_images` table and linked to product.
* Variants: Separate `variants` table (e.g., size, color) with stock/sku per variant.

**Categories & Subcategories**

* `categories` table supports parent-child relationships for subcategories. Category page shows products for that category and child categories.

**Implementation notes**

* Use Eloquent relationships: `Product->images()`, `Product->variants()`, `Category->children()`.
* Validation for image types, file size, and sanitation of inputs.

---

### 3. Product Browsing & Search

* Home Page displays featured/latest products (configurable by admin flag).
* Category Page lists products with pagination and sorting.
* Product Detail page shows images carousel, variant selector, price + discounted price, stock availability, related products, and reviews.
* Search + Filters: Full-text search (MySQL `MATCH`/`FULLTEXT`) or Laravel Scout (Algolia) for better results. Filters: category, price range, rating, availability.

---

### 4. Shopping Cart & Wishlist

* Cart: Add/Update/Remove items. Cart persisted in session for guests and in database linked to user for logged-in users.
* Quantity validation ensures not exceeding available stock.
* Wishlist: Only for logged-in users. Add/remove products from wishlist table.
* Cart summary UI: subtotal, tax (configurable percentage), shipping, coupon discount, grand total.

**Implementation notes**

* Use a `carts` and `cart_items` database design if persistent cart is required.
* When user logs in, merge session cart into user cart.

---

### 5. Checkout & Orders

* Checkout form collects billing/shipping address, payment method (COD, Stripe, Razorpay in code comments), and order note.
* Order confirmation page before finalizing.
* On successful order: create `orders` and `order_items`, reduce stock for product/variant, send confirmation email to user, and notify admin(s).
* Order status lifecycle: `Pending` → `Processing/Shipped` → `Delivered` or `Cancelled`.

**Payment integrations**

* Stripe examples included (server-side payment intents). Razorpay code blocks can be added similarly.
* Use webhooks to verify successful payments and update order status.

---

### 6. Order History & Admin Dashboard

**User**

* View past orders, order items, order status, and tracking updates.

**Admin**

* Dashboard with sales stats (today/week/month), total users, most sold products, order breakdown by status.
* Manage orders: view details, change status, add tracking number, and issue refunds (if integrated with payment gateway).

---

### 7. Reviews & Ratings (optional)

* Authenticated users can leave reviews and rating for a product.
* Reviews may support images or a short video. Images stored with product review images.
* Average rating is computed and displayed on product listing & detail pages.

**WebRTC for video reviews**

* Optional feature to record short video reviews using WebRTC on the client, then upload to the server (chunked if needed). Implement with a JS recorder and an endpoint to receive the file.
* Consider storage and moderation before public display.

---

### 8. Coupons & Discounts (optional)

* Admin can create coupon codes with: `code`, `type` (percentage/fixed), `value`, `starts_at`, `expires_at`, `usage_limit`, `per_user_limit`, `min_cart_value`.
* Coupon validation applied at checkout: expiry, usage count, min cart amount, and applicable categories/products.

---

### 9. Notifications & Emails

* Send email after order confirmation (Mailable classes) and for password resets and email verification.
* Admin notifications (database notifications) for new orders. Display in admin dashboard.
* Use queued mail sending (`ShouldQueue`) to avoid slowing requests. Use `redis`/database queue with `supervisor` in production.

---

### 10. Deployment & Optimization

* Use `.env` for environment configuration.
* Use `php artisan config:cache`, `route:cache`, and `view:cache` for performance.
* Use OPcache on PHP side and set up queues with `supervisor`.
* Serve static assets through a CDN (if using S3 for images) and enable compression.

---

## Tech Stack

* Backend: Laravel (8/9/10+)
* Database: MySQL / MariaDB (or PostgreSQL)
* Frontend: Blade + Bootstrap / Tailwind (or React/Vue for SPA areas)
* Storage: Local (public/) or S3 for images
* Queue: Database / Redis
* Mail: SMTP / Mailgun / SendGrid
* Payment: Stripe / Razorpay (example integrations)

---

## Installation & Setup

1. **Requirements**

   * PHP 8.1+ (depending on Laravel version)
   * Composer
   * Node.js & npm
   * MySQL / MariaDB
   * Redis (optional for queues)

2. **Clone**

```bash
git clone <repo-url> laravel-ecommerce
cd laravel-ecommerce
```

3. **Install dependencies**

```bash
composer install
npm install
npm run dev
```

4. **Environment**

```bash
cp .env.example .env
php artisan key:generate
```

Fill `.env` with DB, MAIL, and PAYMENT provider credentials.

5. **Storage & Migrations**

```bash
php artisan migrate
php artisan db:seed # to seed admin user, categories, products (if seeds provided)
php artisan storage:link
```

6. **Run**

```bash
php artisan serve
# or use valet/homestead/docker in dev
```

---

## Database Schema (High-level)

* `users` (id, name, email, password, role, is_blocked, ...)
* `categories` (id, name, slug, parent_id, description)
* `products` (id, title, slug, description, price, discount, sku, stock_quantity, featured, status)
* `product_images` (id, product_id, path, alt)
* `variants` (id, product_id, type, value, sku, stock_quantity, price_modifier)
* `carts` (id, user_id nullable, session_id, total)
* `cart_items` (id, cart_id, product_id, variant_id, qty, price)
* `wishlists` (id, user_id, product_id)
* `orders` (id, user_id, order_number, subtotal, shipping, tax, total, status, payment_status)
* `order_items` (id, order_id, product_id, variant_id, qty, price)
* `coupons` (id, code, type, value, usage_limit, per_user_limit, starts_at, expires_at)
* `reviews` (id, user_id, product_id, rating, comment, media_path, approved)
* `notifications` (via Laravel built-in notifications)

---

## Important Routes & API Endpoints (examples)

**Web (blade)**

* `GET /` — Home
* `GET /categories/{slug}` — Category listing
* `GET /product/{slug}` — Product detail
* `POST /cart/add` — Add to cart
* `POST /checkout` — Place order

**Admin**

* `GET /admin` — Dashboard
* `GET /admin/products` — Manage products
* `POST /admin/products` — Create product

**API (if applicable)**

* `GET /api/products` — Returns paginated products
* `POST /api/cart` — Add product to cart (auth)
* `POST /api/checkout` — Place order via API

---

## Admin Panel — Functionality & Notes

* Admin roles allow product/category CRUD, user management, order management, coupon creation, and viewing metrics.
* Dashboard widgets: total sales, total orders, new users, low-stock products, top selling products.

**Security**

* Protect admin routes with `is_admin` middleware and prefer a separate guard for admin users.

---

## Checkout Flow & Stock Management

1. Customer places items into cart.
2. During checkout, revalidate item availability and price.
3. Create order with status `Pending`.
4. Reduce stock after payment confirmation (or at order creation for immediate reservation — pick approach consistently).
5. If payment fails or order cancelled, restore stock.

**Notes**

* For concurrent orders, use database transactions and `SELECT ... FOR UPDATE` semantics or optimistic locking to avoid overselling.

---

## Reviews, Ratings & WebRTC (optional)

* Reviews are tied to `user_id` and `product_id` and can be moderated (approved flag).
* For video reviews via WebRTC: implement a client-side recorder (MediaRecorder API), upload resulting file to the server endpoint, and associate with review model. Ensure file size limits and conversion (e.g., MP4) as needed.

---

## Coupon/Discount Rules (optional)

* Validation to check: code exists, not expired, usage limit not exceeded, per-user usage limit not exceeded, min cart value met, applicable products/categories.
* Calculate discount correctly for percentage vs fixed types.

---

## Notifications, Emails & Queues

* Use `Mail::to($user)->queue(new OrderConfirmation($order))` for queued email delivery.
* Use database notifications for admin alerts.
* Set up queue worker (`php artisan queue:work`) with `supervisor` in production.

---

## Deployment Tips & Optimization

* Use environment specific config, disable debug in production.
* Run: `php artisan config:cache && php artisan route:cache && php artisan view:cache`.
* Use Redis for cache and queues when available.
* Use `composer install --optimize-autoloader --no-dev` on deployment.
* Use `storage:link` to make images publicly accessible.

---

## Testing

* Unit & Feature tests using PHPUnit: authentication flows, cart behavior, coupon validation, checkout flow, order creation.
* Dusk for browser tests for critical paths (login, add to cart, checkout).

---

## Security Considerations

* Validate and sanitize all file uploads. Limit type and size.
* Use CSRF protection on web forms (Laravel does this by default).
* Escape output in views to prevent XSS and use prepared statements for DB queries to avoid SQL injection.
* Rate-limit login attempts and critical endpoints.

---

## Environment Variables (sample keys)

```
APP_NAME=Laravel
APP_ENV=local
APP_KEY=
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_ecommerce
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=example@example.com
MAIL_FROM_NAME="Laravel E-Commerce"

STRIPE_KEY=
STRIPE_SECRET=
RAZORPAY_KEY=
RAZORPAY_SECRET=

QUEUE_CONNECTION=database
CACHE_DRIVER=file
SESSION_DRIVER=file
FILESYSTEM_DRIVER=public
```

---

## Useful Artisan Commands

* `php artisan migrate`
* `php artisan db:seed`
* `php artisan storage:link`
* `php artisan queue:work` (or use supervisor)
* `php artisan route:cache` / `config:cache` / `view:cache`

---

## How to extend / Next steps

* Add REST API and JWT auth for mobile apps.
* Add analytics for product views and user behavior.
* Add return/refund flows integrated with payment providers.
* Add multi-currency and tax rules per region.
* Add shipment provider integration (ShipRocket / EasyPost) for tracking automation.

---
