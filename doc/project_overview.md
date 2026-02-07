# TransLab - Transport Ticket Booking System
## Project Overview
**TransLab** is a comprehensive, multi-tenant transport ticket booking solution built on the **Laravel 11** framework. It is designed to facilitate complex fleet management, route scheduling, and ticket sales for bus operators, while providing a seamless booking experience for travelers.

## 1. Technology Stack
- **Backend Framework:** Laravel 11.0 (PHP 8.3+)
- **Frontend:** Laravel Blade Templates
- **Asset Build Tool:** Vite
- **Database:** MySQL
- **Scripting:** jQuery / Vanilla DataTables (Backend), Bootstrap 5

## 2. System Architecture
The application follows a modular MVC (Model-View-Controller) architecture, utilizing Laravel's core features to separate logic for different user roles.

### User Roles
The system is divided into distinct namespaces for security and logical separation:
1.  **Admin (`/admin`)**:
    -   Global system configuration.
    -   User and Fleet Owner management.
    -   Payment gateway and notification settings.
    -   View reports and support tickets.
2.  **Owner (`/owner`)**:
    -   **Fleet Management**: Manage Vehicles, Routes, and Schedules.
    -   **Trip Management**: Create and assign trips to vehicles and drivers.
    -   **Staff Management**: Manage Drivers and Counter Managers.
3.  **Driver (`/driver`)**:
    -   View assigned trips and schedules.
4.  **Counter Manager (`/manager`)**:
    -   Sell tickets physically at counters.
    -   Manage boardings at their specific location.
5.  **User (Public/Frontend)**:
    -   Search and book tickets.
    -   Manage profile and booking history.
    -   Submit support tickets.

### Core Modules & Features
-   **Fleet Management**: Manages vehicles (`Vehicle`), seat layouts (`SeatLayout`), and fleet types (`FleetType`).
-   **Route & Schedule**:
    -   `Route`: Defines the path (Start Points <-> End Points) and stoppages.
    -   `Schedule`: Defines the timing for trips.
    -   `Trip`: The core entity that combines a Route, Schedule, and Vehicle for a specific day/time.
-   **Booking System**:
    -   Dynamic seat selection.
    -   Partial payment support.
    -   Automated ticket generation.
-   **Payment Gateways**: Application supports 20+ automated gateways including Stripe, Razorpay, Mollie, BTCPay, CoinGate, and manual bank transfers.
-   **Notifications**: Integrated email/SMS notifications using Mailjet, Twilio, Vonage, etc.

## 3. Directory Structure Key Highlights
-   `core/app/Http/Controllers`: Contains logic separated by folder (`Admin`, `Owner`, `Driver`, `Manager`).
-   `core/routes`: Separate route files for each role (`web.php`, `admin.php`, `owner.php`, etc.).
-   `core/resources/views/templates/basic`: frontend theme files.
-   `core/app/Providers`: Service providers for app bootstrapping.

## 4. Key Configuration
-   **Environment**: main configuration in `.env` (Database, App URL, Debug mode).
-   **Helpers**: `core/app/Http/Helpers/helpers.php` contains 50+ global helper functions used throughout the blades and controllers.
-   **Permissions**: Middleware based role verification (`auth:admin`, `auth:owner`, etc.).

## 5. Development Guidelines
-   **Adding Features**: Determine the target role (e.g., Owner) and modify the respective Controller and View. Check `core/routes/[role].php` for routing.
-   **Frontend Changes**: Modify files in `core/resources/views/templates/basic`. Use `activeTemplate()` helper for theme compatibility.
-   **Assets**: Run `npm run dev` (Vite) for hot reloading during development of JS/CSS assets.

## 6. Integrations
-   **Google Authenticator**: 2FA support for users.
-   **Social Login**: Google/Facebook login integration via Socialite.
-   **ReCaptcha**: Integrated for spam protection on forms.
