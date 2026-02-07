# Database Schema Documentation

## Overview
The database for **TransLab** is designed around a multi-tenant architecture where **Owners** (Fleet Operators) are the primary users managing their own **Vehicles**, **Routes**, and **Trips**. 
**Note:** There is no dedicated `users` table for end-customers (travelers). Ticket bookings are handled effectively as "guest" bookings or linked to specific administrative users, with passenger details stored directly in the booking record.

## Key Relationships (ERD Highlights)

### 1. Fleet & Schedule Management
*   **Owner** (1) ---- (N) **Vehicle**
*   **Owner** (1) ---- (N) **Route**
*   **Owner** (1) ---- (N) **Schedule**
*   **FleetType** (1) ---- (N) **Vehicle** (Defines layout/seats)

### 2. Trip Operations
The `Trip` entity is the central operational unit.
*   **Trip** belongs to:
    *   `Owner`
    *   `Vehicle`
    *   `Route`
    *   `Schedule`
*   **Trip** has many `BookedTicket`s.

### 3. Booking System
*   **BookedTicket** contains:
    *   `trip_id`: The specific trip instance.
    *   `seats`: (JSON/Text) List of allocated seat numbers.
    *   `passenger_details`: (JSON/Text) Guest passenger info (Name, Gender, etc.).
    *   `source_destination`: (JSON/Text) Pickup and Drop-off IDs.

## Detailed Table Definitions

### `owners` (Fleet Operators)
Primary user account for bus companies.
- `id`: PK
- `username`, `email`, `password`: Auth credentials
- `balance`: Wallet balance
- `status`: Account status

### `trips`
Represents a scheduled journey.
- `id`: PK
- `owner_id`: FK to `owners`
- `fleet_type_id`: FK to `fleet_types` (Defines seat layout)
- `route_id`: FK to `routes`
- `schedule_id`: FK to `schedules` (Time of departure)
- `vehicle_id`: (Implied via assignment or fleet logic)
- `day_off`: List of days the trip does NOT run.
- `status`: Active/Inactive

### `booked_tickets`
- `id`: PK
- `trip_id`: FK to `trips`
- `counter_manager_id`: ID of the staff who sold it (0 if online/owner).
- `seats`: Text/JSON (e.g., `["A1", "A2"]`)
- `passenger_details`: Text/JSON (Passenger Name, Mobile, Email)
- `price`: Total price
- `date_of_journey`: Date
- `status`: 1=Booked, 0=Cancelled, 2=Pending

### `routes`
- `id`: PK
- `name`: Route Name (e.g., "Dhaka to Chittagong")
- `stoppages`: JSON list of intermediate stops.
- `starting_point`, `destination_point`: IDs of `counters`.

### `vehicles`
- `id`: PK
- `register_no`: License plate.
- `fleet_type_id`: FK to `fleet_types` (Links to `seat_layouts`).

### `admins`
System super-administrators.
- `id`, `username`, `password`, `email`.

### Other Key Tables
-   `counters`: Physical ticket counters.
-   `counter_managers`: Staff manning the counters.
-   `drivers`: Drivers assigned to trips.
-   `gateways`: Payment gateway configurations.
-   `general_settings`: Global system configs (site name, currency).

## Notes
-   **JSON Usage**: The system heavily relies on JSON or serialized text in columns like `booked_tickets.passenger_details` and `routes.stoppages` to store structured data without separate child tables for every detail.
-   **No Customer Accounts**: The schema does not enforce a registered "Traveler" entity. Bookings are transactional/guest-based.
