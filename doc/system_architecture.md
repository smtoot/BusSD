# System Architecture Documentation

## 1. High-Level Overview
**TransLab** is a multi-tenant Transport Ticket Booking System built on **Laravel 11**. It serves multiple user roles (Fleet Owners, Counter Managers, Drivers, Travelers) through a role-based access control (RBAC) system.

### Core Stack
*   **Framework**: Laravel 11 (PHP 8.3+)
*   **Database**: MySQL / MariaDB
*   **Frontend**: Blade Templates (Server-Side Rendering) with Bootstrap/jQuery.
*   **API**: RESTful structure (implied via route definition, though primarily web-centric).

## 2. Architectural Pattern: MVC with Role Isolation
The application follows standard MVC but strictly separates logic by user role.

### Controllers
Located in `core/app/Http/Controllers`.
*   `Admin/`: Super-admin logic (System config, Payment Gateway setup, overall monitoring).
*   `Owner/`: Fleet Operator logic (Trip creation, Vehicle assignment, Route management).
*   `Manager/`: Counter staff logic (Ticket sales, Booking management).
*   `Driver/` & `Supervisor/`: Operational staff logic.
*   `SiteController.php`: Public-facing pages (Home, Search, Blog).

### Routes
Located in `core/routes/`.
*   `web.php`: Public routes.
*   `admin.php`: `/admin` prefix.
*   `owner.php`: `/owner` prefix.
*   `manager.php`: `/manager` prefix.
*   *Access is enforced via Middleware definitions in these files.*

## 3. Authentication & Security
The system does **not** usage a single `User` table for all roles. Instead, it uses **Multi-Guard Authentication**:

### Guards (defined in `config/auth.php`)
*   `admin`: Uses `admins` table.
*   `owner`: Uses `owners` table.
*   `manager`: Uses `counter_managers` table.
*   `driver`: Uses `drivers` table.
*   `web`: Uses `owners` table (default fallback).

### Middleware
Role enforcement is handled via specific middleware:
*   `RedirectIfAdmin`, `RedirectIfOwner`, `CheckStatus` (Active/Banned check).
*   Middleware is applied in route groups to secure entire sections of the app.

## 4. Frontend Architecture
The system supports a replaceable **Template** system.
*   **Location**: `core/resources/views/templates/`.
*   **Active Template**: Controlled by database setting or `.env`.
*   **Structure**:
    *   `layouts/`: Master layouts (`frontend.blade.php`, `master.blade.php`).
    *   `sections/`: Reusable UI blocks (Hero, About, etc.) often dynamic via `sections.json`.
    *   `partials/`: Headers, Footers, Breadcrumbs.

## 5. Key Business Logic Modules
### Trip Management
*   **Flow**: Owner creates `FleetType` -> Adds `Vehicle` -> Defines `Route` -> Creates `Schedule` -> Creates `Trip`.
*   **Booking**: `BookedTicket` captures the passenger details and links to a specific `Trip`.

### Payment Processing
*   **Gateways**: extensible `Gateway` module supporting Stripe, PayPal, Razorpay, etc.
*   **Log**: `Deposit` table tracks all incoming payments from owners (wallet top-up) or direct ticket sales.

## 6. Directory Structure Highlights
```
core/
├── app/
│   ├── Http/Controllers/ (Role-based folders)
│   ├── Http/Middleware/ (Role-based guards)
│   ├── Models/ (Eloquent ORM)
│   └── Lib/ (Helper classes: UserNotificationSender, FormProcessor)
├── config/ (App configuration)
├── database/ (Migrations & Seeds)
└── resources/
    └── views/
        ├── admin/ (Admin Panel UI)
        ├── owner/ (Owner Panel UI)
        └── templates/ (Public Frontend Themes)
```
