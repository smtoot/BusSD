# Phase 1: Identity & Trip Search (Implementation Plan)

This document provides the high-level engineering details for Phase 1. The goal is to reach a state where a Flutter app can register a user and perform a global trip search.

---

## 1. Module: Passenger Identity (Backend)
**Objective**: Create a secure authentication system for passengers.

### Technical Tasks:
1. **Database**: Create `passengers` table with:
   - `id`, `firstname`, `lastname`, `email`, `password`, `mobile`, `dial_code`, `status`, `ev`, `sv`, `deleted_at`.
2. **Model**: Generate `Passenger.php` with `Sanctum` tokens and `SoftDeletes`.
3. **Auth Guard**: Define `passenger` guard in `config/auth.php`.
4. **Endpoints**:
   - `POST /api/v1/auth/register`: Takes phone/email, returns "Unverified" state.
   - `POST /api/v1/auth/verify-otp`: Validates Sudanese SMS OTP.
   - `POST /api/v1/auth/login`: Returns Sanctum Bearer Token.

### Acceptance Criteria:
- [ ] Passenger can register via API and receive a success status.
- [ ] Duplicate emails/phones are rejected with 422 Unprocessable Entity.
- [ ] Login returns a valid token that can be used for subsequent requests.
- [ ] Deleted passengers (Soft Delete) cannot log in.

---

## 2. Module: Global Trip Search (The "Engine")
**Objective**: Allow passengers to find buses across all participating companies.

### Technical Tasks:
1. **API Endpoint**: `GET /api/v1/search/trips`.
2. **Filter Logic**:
   - Join `trips` with `routes`.
   - Filter `routes` where `stoppages` JSON contains both `pickup` and `destination`.
   - Ensure `destination` index is > `pickup` index.
3. **Inventory Check**:
   - Cross-reference `booked_tickets` for the specific date.
   - Calculate `remaining_seats = total_seats - booked_seats`.
4. **Pricing**: Fetch sub-segment fare from `ticket_price_by_stoppages`.

### Acceptance Criteria:
- [ ] Search returns trips from multiple "Owners" (Bus Companies).
- [ ] Trips on "Days Off" (e.g., Fridays) are automatically excluded.
- [ ] Fare displayed matches the specific sub-segment defined in the admin panel.
- [ ] API responds in < 300ms for a standard query.

---

## 3. Flutter "V1" (Frontend Integration)
**Objective**: Basic app skeleton with Search and Results.

### Technical Tasks:
1. **Auth Helper**: Implement `AuthInterceptor` in Dio to attach Bearer tokens.
2. **Search Form**: Screen with "From", "To", and Date Selection using `Google Maps` for counter locations.
3. **Result Cards**: List view showing Bus Name, Times, and "Book Now" button.

---

## 4. Phase 1 Definition of Done (DoD)
Phase 1 is complete when a developer can:
1. **Register** a new account on the mobile app.
2. **Login** successfully.
3. **Search** for a trip from "Khartoum" to "Port Sudan".
4. **See** a list of real buses with correct prices and seat counts.
