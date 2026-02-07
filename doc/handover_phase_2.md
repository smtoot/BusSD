# Handover: Phase 2 (Seat Selection & Booking Initiation)

This document records the exact technical changes implemented during Phase 2.

---

## 1. Files Created/Modified
| File Path | Purpose |
| :--- | :--- |
| `core/app/Http/Controllers/Api/Passenger/TripSearchController.php` | Modified: Added `layout` method for seat maps. |
| `core/app/Http/Controllers/Api/Passenger/BookingController.php` | New: Atomic booking initiation logic. |
| `core/database/migrations/2026_02_06_154934_add_passenger_id_to_booked_tickets.php` | New: Linked bookings to the Passenger model. |
| `core/routes/api.php` | Modified: Registered layout and booking endpoints. |

---

## 2. Logic Implemented

### 2.1 Trip Layout API
- **Endpoint**: `GET /api/v1/trip/{id}/layout?date=Y-m-d&pickup_id=X&destination_id=Y`
- **Feature**: Dynamically fetches the bus grid and marks seats as "Booked" by checking existing tickets for that specific journey date.

### 2.2 Atomic Booking Initiation
- **Endpoint**: `POST /api/v1/booking/initiate`
- **Race Condition Protection**: Uses `DB::transaction` and a final seat availability check to prevent two people from booking the same seat simultaneously.
- **Pending State**: Creates a record with `status = 0`, allowing the passenger time to complete payment before the seat is officially "Sold".

---

## 3. Database Updates
- Added `passenger_id` column to the `booked_tickets` table to support the new B2C user base without breaking existing admin/owner logic.

---

**Handover Status**: Phase 2 backend is implemented and ready for Flutter integration.
**Next Reference**: [Phase 3.1 Handover](file:///Users/omerheathrow/Downloads/codecanyon-KActWBoM-translab-transport-ticket-booking-system/Files/doc/handover_phase_3_1.md) (Pending)
