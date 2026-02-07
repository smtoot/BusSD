# B2C Passenger App: Implementation Roadmap

This roadmap details the engineering steps required to transform TransLab from a back-office/counter-based system into a scalable backbone for a consumer-facing mobile application.

---

## Phase 1: Authentication & User Identity (Foundation)
The current system lacks a "Customer" or "Passenger" entity with login capabilities.

### 1.1 The Passenger Model
- **Action**: Create a `Passenger` model and `passengers` table.
- **Fields**: Name, Email (Unique), Phone, Password, Avatar, and `status` (Active/Banned).
- **Laravel Implementation**:
  - Implement `Authenticatable` in the model.
  - Setup a new Auth Guard specifically for passengers: `auth:passenger`.

### 1.2 RESTful Authentication (API)
- **Install Laravel Sanctum**: Use Sanctum for issuing API tokens.
- **Endpoints**:
  - `POST /api/v1/register`: Basic registration + email verification.
  - `POST /api/v1/login`: Issues a Bearer Token.
  - `POST /api/v1/logout`: Revokes token.
  - `GET /api/v1/profile`: Returns passenger booking history.

---

## Phase 2: The Trip Search Engine (Core API)
Currently, trip searching is coupled to the Manager UI.

### 2.1 Search API Endpoints
- **GET `/api/v1/locations`**: Fetches all active `Counters` (Stoppages) for search dropdowns.
- **GET `/api/v1/trips/search`**:
  - **Inputs**: `from_id`, `to_id`, `date`.
  - **Logic**: Reuse logic from `ManagerController::searchTrip`.
  - **JSON Output**: Trip ID, Title, Fare, Arrival/Departure times, and Vehicle type.

### 2.2 Inventory & Seat Mapping
- **GET `/api/v1/trips/{id}/seats`**:
  - Fetches the `FleetType` seat layout.
  - Cross-references `BookedTicket` for the specific `date` to mark "Already Booked" seats.
  - Returns a JSON map of seat grid (e.g., `['A1' => 'available', 'A2' => 'booked']`).

---

## Phase 3: Booking & Payment Flow (Mobile Integration)
This is the most complex transition from Web to Mobile.

### 3.1 Booking Initiation
- **POST `/api/v1/booking/initiate`**:
  - Validates seat availability.
  - Temporarily locks seats (optional, prevents race conditions).
  - Calculates final price + taxes.
  - Creates a `Deposit` (or Payment Intent) record.

### 3.2 Mobile Payments
- **Approach**: Do not use WebViews for the whole flow. 
- **Action**: Adapt the `Gateway/ProcessController` to return:
  - **Stripe**: Client Secret for the Mobile SDK.
  - **Razorpay/Flutterwave**: Order ID for mobile checkout.
  - **Others**: A specific "checkout URL" that can be opened in an In-App Browser.

## Phase 4: Flutter Mobile Development (Frontend)
Since you are using **Flutter**, the following stack and architecture are recommended:

### 4.1 Recommended Tech Stack
- **State Management**: `Provider` (Simple) or `Bloc/Cubit` (Scaling).
- **Networking**: `Dio` (Supports interceptors for Bearer tokens).
- **Storage**: `flutter_secure_storage` for storing API tokens.
- **Maps**: `google_maps_flutter` for displaying counter locations.

### 4.2 Key Flutter Components
- **Auth Flow**: Implement a `Splash` screen that checks for a stored token and redirects to `Home` or `Login`.
- **Search UI**: `DatePicker` for journey date selection and `Searchable Dropdowns` for counters.
- **Seat Grid**: Use `GridView.builder` to render seats dynamically based on the API response from Phase 2.2.
- **Seat Status**: Map the API statuses (`available`, `booked`, `selected`) to custom seat widgets with distinct colors.

### 4.3 Native Features
- **Push Notifications**: Use `firebase_messaging`.
- **PDF Viewing**: Use `flutter_pdfview` or `url_launcher` to show the E-Ticket.
- **Local Receipt**: `path_provider` to download and save the ticket locally on the phone.

---

## Phase 5: Passenger Management & Post-Booking
- **E-Ticket Generation**: Create an API to return a secure URL for the "Ticket PDF" (Reuse `ManagerController::ticketPrint`).
- **Push Notifications**: Integrate Firebase (FCM) using the existing `DeviceToken` model to notify passengers of booking confirmation or bus delays.
- **Booking History**: API to fetch `passengers` related `BookedTicket` records.

---

## Phase 6: Sudan Regional Strategy (SUDAN)
Since the primary market is **Sudan**, certain technical pivots are required to handle local infrastructure and financial ecosystems.

### 6.1 Local Payment Integration
- **Action**: Replace or augment global gateways with **SyberPay**, **Busit**, or local bank mobile APIs (like Bank of Khartoum's `BOK` API if available).
- **Flutter Implementation**: Use native `url_launcher` to deep-link into bank apps or handle USSD-based payment confirmations if required.

### 6.2 Connectivity & Low-Bandwidth Optimization
- **Offline Tickets**: Ensure the Flutter app caches the ticket (JSON/PDF) locally so it can be shown even without an active internet connection.
- **Lightweight Search**: Implement aggressive caching for `locations` and `schedules` to minimize API calls on slow 3G/Edge networks.

### 6.3 SMS-First Communication
- **OTP Reliability**: Integrate with local SMS gateways (e.g., Zain, MTN, Sudani specific APIs) for the Phase 1.1 Registration OTP.
- **WhatsApp Fallback**: Consider using a WhatsApp API for ticket delivery, as it's the most stable data-based communication channel in the region.

---

## Phase 7: Production Readiness (Infrastructure)

### 7.1 Database Scaling
- **Migration**: Move from **SQLite** (local dev) to **MySQL 8.0** or **PostgreSQL**.
- **Reason**: SQLite will lock the database file during concurrent bookings, causing "Database is locked" errors at scale.

### 7.2 Security Layer
- **API Rate Limiting**: Enable Laravel middleware to prevent brute-force on search/auth.
- **CORS Configuration**: Ensure Laravel is configured to allow requests from the mobile app (or specific origins).
- **Data Validation**: Strict input validation to prevent SQL injection or price manipulation.

---

## Summary of Dev Effort
- **Backend API Development**: ~160 - 200 hours.
- **Flutter App Development**: ~200 - 250 hours (Design + Logic).
- **Integration & QA**: ~80 hours.

**Verdict**: TransLab provides the "Engine" (70% of the logic). Your focus should be 100% on the **API Surface** and **Mobile Experience**.
