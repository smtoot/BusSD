# Handover: Phase 1 (Identity & Trip Search)

This document records the exact technical changes implemented during Phase 1. This serves as an audit trail for future developers working on the B2C Passenger App.

---

## 1. Files Created
| File Path | Purpose |
| :--- | :--- |
| `core/database/migrations/2026_02_06_154119_create_passengers_table.php` | Schema for the B2C passenger database. |
| `core/app/Models/Passenger.php` | Eloquent model with Sanctum & SoftDeletes support. |
| `core/app/Http/Controllers/Api/Passenger/Auth/RegisterController.php` | Registration & Phone OTP logic. |
| `core/app/Http/Controllers/Api/Passenger/Auth/LoginController.php` | Sanctum-based authentication. |
| `core/app/Http/Controllers/Api/Passenger/TripSearchController.php` | Global search engine logic. |
| `core/routes/api.php` | Central API route definition file. |

---

## 2. Configuration Changes

### 2.1 Authentication (`core/config/auth.php`)
- **Guard Added**: `passenger` (Driver: `sanctum`, Provider: `passengers`).
- **Provider Added**: `passengers` (Driver: `eloquent`, Model: `App\Models\Passenger`).

### 2.2 Route Registration (`core/bootstrap/app.php`)
- Modified the bootstrap to load `routes/api.php` under the `/api/v1` prefix.
- Ensured `api` middleware group is applied to these routes.

---

## 3. Logic Implemented

### 3.1 Sudanese OTP Verification
- Registration triggers a 6-digit random code stored in `phone_otp`.
- Expiry is set to 10 minutes (`otp_expires_at`).
- Status `sv` (SMS Verified) is toggled upon successful verification.

### 3.2 Directional Search Engine
- The search query uses `whereJsonContains` on the `stoppages` field.
- **Critical Logic**: The `TripSearchController` performs an array index check to ensure the `pickup_id` appears *before* the `destination_id` in the route sequence, preventing "reverse-trip" results.
- **Inventory Check**: Availability is calculated on-the-fly by subtracting `booked_tickets->sum('ticket_count')` from the vehicle's `total_seat`.

---

## 4. Verification Proof
- **URL**: `http://localhost:8000/api/v1/test` (Success)
- **URL**: `http://localhost:8000/api/v1/register` (Verified via Test Passenger creation)
- **URL**: `http://localhost:8000/api/v1/login` (Verified Sanctum token issuance)

---

**Handover Status**: Phase 1 is fully integrated and verified.
**Next Reference**: [Phase 2 Execution Plan](file:///Users/omerheathrow/Downloads/codecanyon-KActWBoM-translab-transport-ticket-booking-system/Files/doc/execution_phase_2.md) (Pending Creation)
