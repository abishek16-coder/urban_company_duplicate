# Service Connect - Setup Instructions & Test Checklist

## Prerequisites
1. PHP 8.0+ with MongoDB extension enabled (`mongodb` in `php.ini`).
2. Composer installed.
3. MongoDB running locally on `mongodb://localhost:27017`.

## Installation & Setup
1. Install dependencies:
   ```bash
   composer install
   ```
2. Seed the database with initial admin, categories, provider, and customer:
   ```bash
   php scripts/seed.php
   ```
3. Run the application locally using PHP built-in server:
   ```bash
   php -S localhost:8000 -t public
   ```

## Test Checklist

### 1. Authentication
- [ ] Login as Admin (`admin@serviceconnect.com` / `Admin@123`)
- [ ] Login as Provider (`provider@serviceconnect.com` / `Provider@123`)
- [ ] Login as Customer (`customer@serviceconnect.com` / `Customer@123`)
- [ ] Register a new customer or provider account.

### 2. Admin Module
- [ ] View statistics dashboard (counts for users, providers, bookings, reports).
- [ ] Add a new category under Manage Categories.

### 3. Provider Module
- [ ] Update business profile details.
- [ ] Add a new service offering.

### 4. Customer Module
- [ ] Browse categories on customer dashboard.
- [ ] Search for services using text search.
- [ ] Book a service by selecting date and time slot.

### 5. Reviews & Reports
- [ ] Submit a review and rating for a booking.
- [ ] Raise a complaint/report on a booking.
