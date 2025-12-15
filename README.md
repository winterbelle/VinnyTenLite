# Vinny Ten Racing 🏁

Vinny Ten Racing is a full-stack e-commerce and service booking web application built for an automotive performance brand. The platform allows users to browse performance products, book services, leave reviews, and securely check out using Stripe — with full admin management on the backend.

This project was built using PHP, MySQL, and Stripe Checkout, with a custom UI designed for performance automotive branding.

---

## 🚗 Features

### 🛒 Shop & Products
- Dynamic product listing pulled from the database
- Individual product pages with full descriptions
- Add to cart functionality using PHP sessions
- Cart item count displayed in the header
- Guest checkout supported

### 💳 Secure Checkout
- Stripe Checkout integration (test mode)
- Order creation and tracking
- Payment success handling
- Cart automatically clears after successful payment

### 📦 Orders
- Orders stored in the database
- Order status tracking (`pending`, `completed`, `cancelled`)
- Guest orders supported
- Optional account creation after purchase

### 🛠 Services & Bookings
- List of available performance services
- Logged-in users can book services
- Booking validation (future dates only)
- Services stored dynamically from database

### ⭐ Reviews
- Customers can leave reviews
- Star-based rating system
- Reviews displayed dynamically
- Review form toggles open/closed for cleaner UI

### 👤 User System
- User authentication (login / register)
- Role-based access (admin vs user)
- Edit profile page
- View orders page

### 🔐 Admin Panel
- Add products (image upload supported)
- Edit and delete products
- Manage services and bookings
- Admin-only access protection

---

## 🧱 Tech Stack

- **Frontend:** HTML, CSS (custom styling)
- **Backend:** PHP (MySQLi)
- **Database:** MySQL
- **Payments:** Stripe Checkout
- **Sessions:** PHP Sessions
- **Security:** CSRF protection, prepared statements

---

## 🗄 Database Overview

Key tables include:
- `products`
- `orders`
- `services`
- `bookings`
- `service_reviews`
- `users`

Foreign keys are enforced where applicable, and prepared statements are used to prevent SQL injection.

---

## 🔑 Stripe Setup (Important)

Stripe keys are **NOT committed to GitHub**.

To run Stripe locally:

1. Create a `.env` file (ignored by Git)
2. Add your test keys:
   ```env
   STRIPE_SECRET_KEY=sk_test_XXXXXXXX
   STRIPE_PUBLIC_KEY=pk_test_XXXXXXXX
3. Stripe keys are loaded via config.php / stripe_config.php

---

## 🚀 Running the Project Locally

Clone the repo

Place the project in your local server directory (XAMPP / MAMP)

Import the MySQL database

Create .env file with Stripe keys

Run composer install to install Stripe SDK

Start Apache & MySQL

Visit http://localhost/Vinny-Ten-Lite

---

## 📌 Notes

Stripe is currently in test mode

Guest checkout is supported

Admin routes are protected by role checks

Styling is custom-built to match automotive performance branding

---

### 🖤 Credits

Built with stubbornness, caffeine, and questionable sleep by  

**Blanca Altamirano**  
**Gavin Bryce**  
**Abu Marah**

> If it works, we meant it.  
> If it breaks, it’s Stripe’s fault. 😌🔥
