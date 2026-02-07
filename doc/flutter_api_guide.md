# TransLab B2C API -- Flutter Integration Guide

> **Version**: 1.0
> **Last updated**: 2026-02-07
> **Backend**: Laravel 11 with Sanctum
> **Audience**: Flutter developers integrating the TransLab passenger mobile app

---

## Table of Contents

1. [API Fundamentals](#1-api-fundamentals)
2. [Authentication Flow](#2-authentication-flow)
3. [Response Contract](#3-response-contract)
4. [Rate Limits](#4-rate-limits)
5. [HTTP Status Codes](#5-http-status-codes)
6. [Endpoint Reference](#6-endpoint-reference)
   - [Auth Endpoints](#61-auth-endpoints)
   - [Profile Endpoints](#62-profile-endpoints)
   - [Search & Trip Endpoints](#63-search--trip-endpoints)
   - [Booking & Payment Endpoints](#64-booking--payment-endpoints)
   - [Ticket Management Endpoints](#65-ticket-management-endpoints)
7. [Complete Booking Flow](#7-complete-booking-flow)
8. [Booking Status Codes](#8-booking-status-codes)
9. [Known Limitations & Workarounds](#9-known-limitations--workarounds)
10. [Recommended Dart Models](#10-recommended-dart-models)
11. [Flutter Architecture Recommendations](#11-flutter-architecture-recommendations)

---

## 1. API Fundamentals

| Property | Value |
|---|---|
| **Base URL** | `https://{domain}/api/v1` |
| **Authentication** | Laravel Sanctum Bearer tokens |
| **Required Header (all requests)** | `Accept: application/json` |
| **Content-Type (JSON bodies)** | `Content-Type: application/json` |
| **Content-Type (image uploads)** | `Content-Type: multipart/form-data` |

**Example authenticated request:**

```http
GET /api/v1/passenger/profile HTTP/1.1
Host: your-domain.com
Accept: application/json
Authorization: Bearer 1|abc123def456...
```

---

## 2. Authentication Flow

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│  Register    │────>│  Store Token │────>│  Verify OTP │────>│  App Ready  │
│  POST /register    │  Locally     │     │  POST /verify│     │             │
└─────────────┘     └─────────────┘     │  -otp        │     └─────────────┘
                                         └──────┬───────┘
                                                │ OTP expired?
                                                v
                                         ┌──────────────┐
                                         │  Resend OTP  │
                                         │  POST /resend│
                                         │  -otp        │
                                         └──────────────┘
```

**Key points:**

- A Sanctum token is issued immediately at registration (before OTP verification).
- The token should be stored securely and sent with `/verify-otp` and `/resend-otp`.
- Check the `sv` field (SMS verified) on the passenger object to determine verification status: `0` = unverified, `1` = verified.
- Use `otp_expires_at` from the passenger object to display a countdown timer in the UI.
- The OTP resend endpoint enforces a 2-minute cooldown since the last OTP generation.

---

## 3. Response Contract

Every endpoint returns a unified JSON structure. Parse all responses with a single model.

### Success Response

```json
{
    "status": "success",
    "message": "Optional human-readable message",
    "data": { }
}
```

- `status` is always `"success"`.
- `message` is optional; may be absent on data-only responses.
- `data` contains the payload. Can be an object, array, or paginated structure depending on the endpoint.

### Error Response

```json
{
    "status": "error",
    "remark": "optional_machine_code",
    "message": {
        "error": ["Human-readable error 1", "Human-readable error 2"]
    }
}
```

- `status` is always `"error"`.
- `remark` is a machine-readable code. It only appears on two specific status codes:
  - `401` responses: `"unauthenticated"`
  - `429` responses: `"too_many_requests"`
- `message.error` is always an array of strings. Display the first item to the user; log the rest.

### Validation Error Response (422)

Validation errors follow the same error structure. Each field may have multiple error strings:

```json
{
    "status": "error",
    "message": {
        "error": ["The email field is required.", "The password must be at least 6 characters."]
    }
}
```

---

## 4. Rate Limits

Three rate-limit tiers are applied server-side. Exceeding any limit returns HTTP 429.

| Tier | Limit | Applied To |
|---|---|---|
| `api` | 120 requests / minute | All API endpoints (keyed by user ID or IP) |
| `auth` | 15 requests / minute | `/register`, `/login`, `/verify-otp`, `/resend-otp` (keyed by IP) |
| `booking` | 30 requests / minute | `/booking/initiate`, `/payment/initiate`, `/payment/manual/confirm` (keyed by user ID or IP) |

**429 response body:**

```json
{
    "remark": "too_many_requests",
    "status": "error",
    "message": {
        "error": ["Too many requests. Please try again in a minute."]
    }
}
```

**Flutter implementation:** Check for the `Retry-After` header in 429 responses and back off accordingly. Disable submit buttons during API calls to prevent duplicate submissions.

---

## 5. HTTP Status Codes

| Code | Meaning | When It Occurs |
|---|---|---|
| `200` | Success | Standard success for all GET and most POST endpoints |
| `201` | Created | Registration only (`POST /register`) |
| `400` | Business Logic Error | Seat taken, already rated, cancellation blocked, OTP expired, etc. |
| `401` | Unauthorized | Missing or invalid Bearer token |
| `403` | Forbidden | Account has been banned |
| `404` | Not Found | Resource does not exist or is not owned by the authenticated user |
| `422` | Validation Error | Request body fails Laravel validation rules |
| `429` | Rate Limited | Too many requests (see rate limit tiers above) |
| `500` | Server Error | Unexpected failure; generic message returned, details logged server-side |

---

## 6. Endpoint Reference

### Summary Table

| # | Method | Endpoint | Auth | Throttle | Description |
|---|---|---|---|---|---|
| 1 | POST | `/register` | No | `auth` | Create a new passenger account |
| 2 | POST | `/verify-otp` | Yes | `auth` | Verify phone via 6-digit OTP |
| 3 | POST | `/resend-otp` | Yes | `auth` | Resend OTP to phone |
| 4 | POST | `/login` | No | `auth` | Authenticate and receive token |
| 5 | POST | `/logout` | Yes | -- | Revoke current token |
| 6 | GET | `/passenger/profile` | Yes | -- | Retrieve profile data |
| 7 | POST | `/passenger/profile` | Yes | -- | Update profile (with image) |
| 8 | GET | `/locations` | No | -- | List all pickup/dropoff locations |
| 9 | GET | `/search` | No | -- | Search available trips |
| 10 | GET | `/trip/{id}/layout` | No | -- | Get bus seat layout for a trip |
| 11 | POST | `/booking/initiate` | Yes | `booking` | Reserve seats and create booking |
| 12 | GET | `/passenger/trips/upcoming` | Yes | -- | Upcoming confirmed trips |
| 13 | GET | `/passenger/trips/history` | Yes | -- | Past trip history |
| 14 | GET | `/ticket/{id}/view` | Yes | -- | View ticket details + QR data |
| 15 | POST | `/ticket/{id}/cancel` | Yes | -- | Cancel a ticket (refund request) |
| 16 | POST | `/ticket/{id}/rate` | Yes | -- | Rate a completed trip |
| 17 | GET | `/payment/methods` | Yes | -- | List available payment gateways |
| 18 | POST | `/payment/initiate` | Yes | `booking` | Start payment for a booking |
| 19 | POST | `/payment/manual/confirm` | Yes | `booking` | Submit manual payment proof |

---

### 6.1 Auth Endpoints

#### POST /register

Create a new passenger account. Issues a Sanctum token immediately and sends a 6-digit OTP via SMS.

**Throttle:** `auth` (15/min per IP)

**Request body:**

```json
{
    "firstname": "Ahmed",
    "lastname": "Hassan",
    "email": "ahmed@example.com",
    "mobile": "912345678",
    "dial_code": "+249",
    "password": "secret123",
    "password_confirmation": "secret123"
}
```

**Validation rules:**

| Field | Rules |
|---|---|
| `firstname` | required, string, max:40 |
| `lastname` | required, string, max:40 |
| `email` | required, email, unique:passengers |
| `mobile` | required, string, unique:passengers |
| `dial_code` | required, string |
| `password` | required, string, min:6, confirmed |
| `password_confirmation` | required |

**Response (201 Created):**

```json
{
    "status": "success",
    "message": "Registration successful. Please verify your phone.",
    "data": {
        "token": "1|abc123def456...",
        "passenger": {
            "id": 1,
            "firstname": "Ahmed",
            "lastname": "Hassan",
            "email": "ahmed@example.com",
            "mobile": "912345678",
            "dial_code": "+249",
            "status": 1,
            "ev": 0,
            "sv": 0,
            "otp_expires_at": "2026-02-07T12:10:00.000000Z",
            "created_at": "2026-02-07T12:00:00.000000Z",
            "updated_at": "2026-02-07T12:00:00.000000Z"
        }
    }
}
```

**Flutter notes:**

- Store `data.token` in `flutter_secure_storage` immediately.
- The `phone_otp` field is in the model's `$hidden` array and never appears in responses.
- Use `otp_expires_at` to show a countdown timer (typically 10 minutes from registration).
- Navigate to the OTP verification screen after successful registration.

---

#### POST /verify-otp

Verify the passenger's phone number using the 6-digit OTP sent via SMS.

**Auth required:** Yes (Bearer token from registration or login)
**Throttle:** `auth` (15/min per IP)

**Request body:**

```json
{
    "otp": "482913"
}
```

**Validation rules:**

| Field | Rules |
|---|---|
| `otp` | required, string, size:6 |

**Response (200):**

```json
{
    "status": "success",
    "message": "Phone verified successfully."
}
```

**Error responses:**

| Status | Message |
|---|---|
| 400 | `"Invalid OTP."` |
| 400 | `"OTP has expired."` |

**Flutter notes:**

- On success, update the local passenger object's `sv` field to `1`.
- On "OTP has expired", prompt the user to resend via `/resend-otp`.

---

#### POST /resend-otp

Request a new OTP to be sent to the passenger's phone. Enforces a 2-minute cooldown.

**Auth required:** Yes
**Throttle:** `auth` (15/min per IP)

**Request body:** Empty (no body needed)

**Response (200):**

```json
{
    "status": "success",
    "message": "OTP has been resent to your phone."
}
```

**Error responses:**

| Status | Message |
|---|---|
| 400 | `"Phone already verified."` |
| 429 | `"Please wait before requesting a new OTP."` |

**Flutter notes:**

- The 429 error here is a business-logic cooldown (not the HTTP rate limiter). It fires if fewer than 2 minutes have passed since the last OTP was generated.
- After a successful resend, refresh `otp_expires_at` by calling `GET /passenger/profile` or by adding 10 minutes to the current time locally.
- Disable the "Resend" button for 2 minutes after each successful resend.

---

#### POST /login

Authenticate an existing passenger and receive a new Sanctum token.

**Throttle:** `auth` (15/min per IP)

**Request body:**

```json
{
    "username": "ahmed@example.com",
    "password": "secret123"
}
```

**Validation rules:**

| Field | Rules |
|---|---|
| `username` | required (accepts either email or mobile number) |
| `password` | required |

**Response (200):**

```json
{
    "status": "success",
    "message": "Login successful.",
    "data": {
        "token": "2|def456ghi789...",
        "passenger": {
            "id": 1,
            "firstname": "Ahmed",
            "lastname": "Hassan",
            "email": "ahmed@example.com",
            "mobile": "912345678",
            "dial_code": "+249",
            "status": 1,
            "ev": 0,
            "sv": 1,
            "otp_expires_at": null,
            "created_at": "2026-02-07T12:00:00.000000Z",
            "updated_at": "2026-02-07T12:00:00.000000Z"
        }
    }
}
```

**Error responses:**

| Status | Message |
|---|---|
| 401 | `"Invalid credentials."` |
| 403 | `"Your account has been banned."` |

**Flutter notes:**

- Replace any previously stored token with the new one.
- Check `sv` (SMS verified): if `0`, redirect to the OTP verification screen.
- Check `status`: if banned (403), show a permanent error screen with no retry option.

---

#### POST /logout

Revoke the current Sanctum token (server-side deletion).

**Auth required:** Yes

**Request body:** Empty

**Response (200):**

```json
{
    "status": "success",
    "message": "Logged out successfully."
}
```

**Flutter notes:**

- Delete the token from `flutter_secure_storage` regardless of the API response (even on network failure).
- Clear all cached user data and navigate to the login screen.

---

### 6.2 Profile Endpoints

#### GET /passenger/profile

Retrieve the authenticated passenger's profile.

**Auth required:** Yes

**Response (200):**

```json
{
    "status": "success",
    "data": {
        "passenger": {
            "id": 1,
            "firstname": "Ahmed",
            "lastname": "Hassan",
            "email": "ahmed@example.com",
            "dial_code": "+249",
            "mobile": "912345678",
            "image": null,
            "status": 1,
            "ev": 0,
            "sv": 1,
            "profile_complete": 0,
            "created_at": "2026-02-07T12:00:00.000000Z"
        }
    }
}
```

**Field reference:**

| Field | Type | Description |
|---|---|---|
| `id` | int | Unique passenger ID |
| `firstname` | string | First name |
| `lastname` | string | Last name |
| `email` | string | Email address |
| `dial_code` | string | Phone dial code (e.g., "+249") |
| `mobile` | string | Phone number (without dial code) |
| `image` | string or null | Profile image filename, or null if not set |
| `status` | int | Account status: `1` = active |
| `ev` | int | Email verified: `0` = no, `1` = yes |
| `sv` | int | SMS/phone verified: `0` = no, `1` = yes |
| `profile_complete` | int | `0` = incomplete, `1` = complete |
| `created_at` | string | ISO 8601 timestamp |

---

#### POST /passenger/profile

Update the authenticated passenger's profile. Supports image upload via multipart/form-data.

**Auth required:** Yes
**Content-Type:** `multipart/form-data`

**Request fields:**

| Field | Rules | Notes |
|---|---|---|
| `firstname` | sometimes, string, max:40 | Optional; only include to change |
| `lastname` | sometimes, string, max:40 | Optional; only include to change |
| `image` | sometimes, image, mimes:jpg,jpeg,png, max:2048 | Max 2 MB |

**Response (200):**

```json
{
    "status": "success",
    "message": "Profile updated successfully.",
    "data": {
        "passenger": {
            "id": 1,
            "firstname": "Ahmed",
            "lastname": "Hassan",
            "email": "ahmed@example.com",
            "dial_code": "+249",
            "mobile": "912345678",
            "image": "profile_1_1707307200.jpg",
            "status": 1,
            "ev": 0,
            "sv": 1,
            "profile_complete": 1,
            "created_at": "2026-02-07T12:00:00.000000Z"
        }
    }
}
```

**Flutter notes (Dio multipart example):**

```dart
final formData = FormData.fromMap({
  'firstname': 'Ahmed',
  'image': await MultipartFile.fromFile(
    imagePath,
    filename: 'profile.jpg',
    contentType: MediaType('image', 'jpeg'),
  ),
});

final response = await dio.post('/passenger/profile', data: formData);
```

---

### 6.3 Search & Trip Endpoints

#### GET /locations

Retrieve all active pickup and dropoff locations. No authentication required.

**Response (200):**

```json
{
    "status": "success",
    "data": [
        {"id": 1, "name": "Khartoum Central", "location": "Downtown"},
        {"id": 2, "name": "Port Sudan Terminal", "location": "Harbor District"},
        {"id": 3, "name": "Atbara Station", "location": "City Center"}
    ]
}
```

**Flutter notes:**

- This returns all active Counter records. The list is not paginated (typically static and small).
- Cache this locally and refresh on app launch or pull-to-refresh. Use it to populate pickup and destination dropdowns.
- Both `name` and `location` are human-readable strings. Display as `"name (location)"` or similar.

---

#### GET /search

Search for available trips between two locations on a given date. No authentication required.

**Query parameters:**

| Param | Rules | Example |
|---|---|---|
| `pickup_id` | required, integer | `1` |
| `destination_id` | required, integer | `2` |
| `date` | required, date_format:Y-m-d, after_or_equal:today | `2026-03-15` |

**Example request:**

```
GET /api/v1/search?pickup_id=1&destination_id=2&date=2026-03-15
```

**Response (200):**

```json
{
    "status": "success",
    "data": [
        {
            "trip_id": 5,
            "owner_name": "Nile Express",
            "bus_type": "VIP 40-Seater",
            "departure_time": "08:00:00",
            "arrival_time": "14:30:00",
            "fare": 5000,
            "available_seats": 12,
            "route_name": "Khartoum - Port Sudan"
        },
        {
            "trip_id": 8,
            "owner_name": "Desert Line",
            "bus_type": "Standard 52-Seater",
            "departure_time": "10:00:00",
            "arrival_time": "16:00:00",
            "fare": 3500,
            "available_seats": 30,
            "route_name": "Khartoum - Port Sudan"
        }
    ]
}
```

**Field reference:**

| Field | Type | Description |
|---|---|---|
| `trip_id` | int | Use this for `/trip/{id}/layout` and booking |
| `owner_name` | string | Bus operator company name |
| `bus_type` | string | Fleet/bus type description |
| `departure_time` | string | HH:MM:SS format |
| `arrival_time` | string | HH:MM:SS format |
| `fare` | number | Per-seat price. Total = fare * number_of_seats |
| `available_seats` | int | Number of seats still bookable |
| `route_name` | string | Full route name |

**Business logic applied server-side:**

- Trips with no pricing or fare <= 0 are excluded.
- Trips on their scheduled day off are excluded.
- Directional validation is enforced: the pickup stop must come before the destination stop in the route's stoppage order.

---

#### GET /trip/{id}/layout

Retrieve the seat layout and booked seats for a specific trip on a given date.

**Path parameters:**

| Param | Description |
|---|---|
| `id` | Trip ID (from search results) |

**Query parameters:**

| Param | Rules | Example |
|---|---|---|
| `date` | required, date_format:Y-m-d, after_or_equal:today | `2026-03-15` |
| `pickup_id` | required, integer | `1` |
| `destination_id` | required, integer | `2` |

**Example request:**

```
GET /api/v1/trip/5/layout?date=2026-03-15&pickup_id=1&destination_id=2
```

**Response (200):**

```json
{
    "status": "success",
    "data": {
        "trip_id": 5,
        "bus_name": "Morning Express",
        "layout": {
            "name": "Standard 2x2",
            "total_seats": 40,
            "deck": 1
        },
        "seats": {
            "A1": {"row": 1, "col": 1, "type": "seat"},
            "A2": {"row": 1, "col": 2, "type": "seat"},
            "": {"row": 1, "col": 3, "type": "aisle"},
            "A3": {"row": 1, "col": 4, "type": "seat"},
            "A4": {"row": 1, "col": 5, "type": "seat"}
        },
        "booked_seats": ["A1", "A2", "B3"]
    }
}
```

**Field reference:**

| Field | Type | Description |
|---|---|---|
| `trip_id` | int | Confirms the trip |
| `bus_name` | string | Trip title/name |
| `layout.name` | string | Layout template name |
| `layout.total_seats` | int | Total seat capacity |
| `layout.deck` | int | Number of decks (1 or 2) |
| `seats` | object | Seat grid configuration (FleetType.seats JSON). Keys are seat labels. |
| `booked_seats` | array of strings | Seat labels that are unavailable (sold + operator-locked B2C seats) |

**Flutter notes:**

- Render the seat map by iterating over the `seats` object. Each key is the seat label; the value describes its grid position and type.
- Any seat label found in `booked_seats` must be rendered as **unavailable** (greyed out, non-selectable).
- Empty string keys represent aisles or gaps in the layout grid.
- For double-decker buses (`deck: 2`), the seat grid will include deck indicators; handle both decks in the UI.

---

### 6.4 Booking & Payment Endpoints

#### POST /booking/initiate

Reserve selected seats and create a pending booking. This is atomic and race-condition safe (uses a database transaction with row locking).

**Auth required:** Yes
**Throttle:** `booking` (30/min)

**Request body:**

```json
{
    "trip_id": 5,
    "date": "2026-03-15",
    "pickup_id": 1,
    "destination_id": 2,
    "seats": ["A3", "A4"],
    "passenger_details": [
        {
            "name": "Ahmed Hassan",
            "mobile": "912345678",
            "gender": "male"
        },
        {
            "name": "Fatima Ali",
            "mobile": "987654321",
            "gender": "female"
        }
    ]
}
```

**Validation rules:**

| Field | Rules |
|---|---|
| `trip_id` | required, exists:trips,id |
| `date` | required, date_format:Y-m-d, after_or_equal:today |
| `pickup_id` | required, integer |
| `destination_id` | required, integer |
| `seats` | required, array |
| `seats.*` | required, string |
| `passenger_details` | required, array |
| `passenger_details.*.name` | required, string, max:100 |
| `passenger_details.*.mobile` | nullable, string, max:20 |
| `passenger_details.*.gender` | nullable, in:male,female |

**IMPORTANT:** The `passenger_details` array length **must** match the `seats` array length. Each entry in `passenger_details` corresponds to the seat at the same index.

**Response (200):**

```json
{
    "status": "success",
    "message": "Booking initiated. Please proceed to payment.",
    "data": {
        "trx": "ABCDEF123456",
        "amount": 10000,
        "booking_id": 42
    }
}
```

**Field reference:**

| Field | Type | Description |
|---|---|---|
| `trx` | string | Transaction reference (used in payment flow) |
| `amount` | number | Total price (fare * seat count) |
| `booking_id` | int | Booking record ID (used in payment initiation) |

**Error responses:**

| Status | Message |
|---|---|
| 403 | `"Account banned."` |
| 400 | `"Seat X is reserved for counter booking only."` |
| 400 | `"One or more selected seats are already booked."` |
| 422 | `"Passenger details count must match the number of seats."` |
| 500 | `"Something went wrong. Please try again."` |

**Flutter notes:**

- On 400 "seats already booked", refresh the seat layout (`GET /trip/{id}/layout`) and prompt the user to re-select seats.
- The booking is created with status `0` (pending/unpaid). The user must complete payment to confirm.
- Store `trx`, `amount`, and `booking_id` locally to continue the payment flow.

---

#### GET /payment/methods

Retrieve available payment gateways for the passenger.

**Auth required:** Yes

**Response (200):**

```json
{
    "status": "success",
    "data": [
        {
            "id": 1,
            "method_code": 1000,
            "currency": "SDG",
            "method": {
                "id": 1,
                "name": "Bank Transfer",
                "description": "Transfer to our bank account",
                "image": "bank_transfer.png"
            }
        },
        {
            "id": 2,
            "method_code": 101,
            "currency": "USD",
            "method": {
                "id": 2,
                "name": "PayPal",
                "description": "Pay via PayPal",
                "image": "paypal.png"
            }
        }
    ]
}
```

**Gateway type detection:**

| Condition | Type | Meaning |
|---|---|---|
| `method_code >= 1000` | Manual | Bank transfer, mobile money, etc. User submits proof. |
| `method_code < 1000` | Automatic | PayPal, Stripe, etc. Redirect-based or SDK-based. |

**Flutter notes:**

- Display gateway `method.name`, `method.description`, and `method.image`.
- Use the `method_code` and `currency` values when calling `/payment/initiate`.
- Manual gateways require a subsequent call to `/payment/manual/confirm`.
- Automatic gateways may require WebView handling (see Known Limitations).

---

#### POST /payment/initiate

Start the payment process for a pending booking.

**Auth required:** Yes
**Throttle:** `booking` (30/min)

**Request body:**

```json
{
    "booking_id": 42,
    "method_code": 1000,
    "currency": "SDG"
}
```

**Validation rules:**

| Field | Rules |
|---|---|
| `booking_id` | required, exists:booked_tickets,id |
| `method_code` | required |
| `currency` | required |

**Charge calculation (server-side):**

```
charge = fixed_charge + (amount * percent_charge / 100)
final_amount = (amount + charge) * rate
```

**Response for manual gateway (200):**

```json
{
    "status": "success",
    "data": {
        "trx": "ABCDEF123456",
        "type": "manual",
        "instructions": "<p>Please transfer <b>10,500 SDG</b> to Bank of Khartoum, Account: 12345678. Include your transaction reference <b>ABCDEF123456</b> in the transfer note.</p>"
    }
}
```

**Response for automatic gateway (200):**

```json
{
    "status": "success",
    "data": {
        "trx": "ABCDEF123456",
        "type": "automatic",
        "process_data": {
            "redirect_url": "https://gateway.example.com/pay/abc123",
            "method": "GET"
        }
    }
}
```

**Flutter notes:**

- For `type: "manual"`: Render `instructions` as HTML (use `flutter_html` or `flutter_widget_from_html`). Then collect proof and submit via `/payment/manual/confirm`.
- For `type: "automatic"`: The `process_data` content varies by gateway. See Known Limitations section for WebView guidance.
- The `trx` value is the same one returned from `/booking/initiate`.

---

#### POST /payment/manual/confirm

Submit payment proof for a manual gateway.

**Auth required:** Yes
**Throttle:** `booking` (30/min)

**Request body:**

The body includes `trx` plus dynamic form fields defined by the specific gateway configuration. Common fields include receipt images and transaction IDs.

```json
{
    "trx": "ABCDEF123456",
    "transaction_id": "BANK-REF-789",
    "screenshot": "(file upload)"
}
```

| Field | Rules | Notes |
|---|---|---|
| `trx` | required | Transaction reference from booking/initiate |
| *(dynamic)* | varies | Additional fields depend on the gateway's form configuration |

**Response (200):**

```json
{
    "status": "success",
    "message": "Your payment proof has been submitted and is pending approval.",
    "data": {
        "trx": "ABCDEF123456",
        "status": "Pending"
    }
}
```

**Flutter notes:**

- After submission, show a "pending approval" screen with the transaction reference.
- The booking remains at status `0` until an admin approves the payment, at which point it moves to status `1`.
- Periodically check `GET /ticket/{id}/view` or `GET /passenger/trips/upcoming` to detect when the booking is confirmed.

---

### 6.5 Ticket Management Endpoints

#### GET /passenger/trips/upcoming

Retrieve the authenticated passenger's upcoming confirmed trips.

**Auth required:** Yes

**Query parameters:**

| Param | Default | Max | Description |
|---|---|---|---|
| `page` | 1 | -- | Page number |
| `per_page` | 15 | 50 | Results per page |

**Response (200):**

```json
{
    "status": "success",
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 42,
                "owner_id": 1,
                "trip_id": 5,
                "passenger_id": 2,
                "source_destination": [1, 2],
                "pick_up_point": 1,
                "dropping_point": 2,
                "seats": ["A3", "A4"],
                "passenger_details": [
                    {"name": "Ahmed Hassan", "mobile": "912345678", "gender": "male"},
                    {"name": "Fatima Ali", "mobile": "987654321", "gender": "female"}
                ],
                "ticket_count": 2,
                "date_of_journey": "03/15/2026",
                "status": 1,
                "trx": "ABCDEF123456",
                "price": 10000,
                "created_at": "2026-02-07T12:00:00.000000Z",
                "updated_at": "2026-02-07T12:00:00.000000Z",
                "trip": {
                    "id": 5,
                    "title": "Morning Express",
                    "owner_id": 1,
                    "owner": {
                        "id": 1,
                        "lastname": "Nile Express"
                    },
                    "route": {
                        "id": 3,
                        "name": "Khartoum - Port Sudan",
                        "stoppages": ["1", "2", "3"]
                    },
                    "schedule": {
                        "id": 8,
                        "start_from": "08:00:00",
                        "end_at": "14:30:00"
                    }
                }
            }
        ],
        "first_page_url": "https://domain.com/api/v1/passenger/trips/upcoming?page=1",
        "last_page": 3,
        "last_page_url": "https://domain.com/api/v1/passenger/trips/upcoming?page=3",
        "next_page_url": "https://domain.com/api/v1/passenger/trips/upcoming?page=2",
        "per_page": 15,
        "prev_page_url": null,
        "total": 42
    }
}
```

**Filter logic:** Returns only tickets where `passenger_id` = current user, `status` = 1 (confirmed), and `date_of_journey` >= today.

**IMPORTANT:** See Known Limitations regarding `date_of_journey` string comparison.

---

#### GET /passenger/trips/history

Retrieve the authenticated passenger's past trips.

**Auth required:** Yes

**Query parameters:** Same as `/passenger/trips/upcoming` (`page`, `per_page`).

**Response (200):** Same structure as upcoming trips, but filtered for `date_of_journey` < today, ordered by date descending (most recent first).

---

#### GET /ticket/{id}/view

Retrieve full ticket details including QR code data. Only returns tickets owned by the authenticated passenger.

**Auth required:** Yes

**Path parameters:**

| Param | Description |
|---|---|
| `id` | Booked ticket ID |

**Response (200):**

```json
{
    "status": "success",
    "data": {
        "ticket": {
            "id": 42,
            "trx": "ABCDEF123456",
            "seats": ["A3", "A4"],
            "passenger_details": [
                {"name": "Ahmed Hassan", "mobile": "912345678", "gender": "male"},
                {"name": "Fatima Ali", "mobile": "987654321", "gender": "female"}
            ],
            "date_of_journey": "03/15/2026",
            "price": 10000,
            "status": 1,
            "ticket_count": 2,
            "trip": {
                "id": 5,
                "title": "Morning Express",
                "owner": {
                    "id": 1,
                    "lastname": "Nile Express"
                },
                "route": {
                    "id": 3,
                    "name": "Khartoum - Port Sudan"
                },
                "schedule": {
                    "id": 8,
                    "start_from": "08:00:00",
                    "end_at": "14:30:00"
                },
                "fleetType": {
                    "id": 1,
                    "name": "VIP 40-Seater"
                }
            }
        },
        "qr_data": {
            "trx": "ABCDEF123456",
            "passenger": "Ahmed Hassan",
            "bus": "Morning Express",
            "seats": ["A3", "A4"],
            "date": "03/15/2026"
        }
    }
}
```

**Error responses:**

| Status | Message |
|---|---|
| 404 | Ticket not found or not owned by the authenticated passenger |

**Flutter notes:**

- Use the `qr_flutter` package to generate a QR code from `qr_data` serialized as a JSON string.
- The `qr_data` object is specifically shaped for ground staff QR scanners. Do not modify its structure.

**QR code generation example:**

```dart
import 'dart:convert';
import 'package:qr_flutter/qr_flutter.dart';

QrImageView(
  data: jsonEncode(qrData),
  version: QrVersions.auto,
  size: 200.0,
)
```

---

#### POST /ticket/{id}/cancel

Request cancellation of a confirmed ticket. Refund amount depends on how far in advance the cancellation is made.

**Auth required:** Yes

**Path parameters:**

| Param | Description |
|---|---|
| `id` | Booked ticket ID |

**Request body:** Empty

**Refund tiers:**

| Time Before Departure | Refund Percentage |
|---|---|
| More than 24 hours | 90% |
| 12 to 24 hours | 70% |
| 2 to 12 hours | 50% |
| Less than 2 hours | **Blocked** (400 error) |

**Response (200):**

```json
{
    "status": "success",
    "message": "Cancellation request submitted. Expected refund: 9000 SDG",
    "data": {
        "refund_amount": 9000,
        "refund_percent": 90,
        "ticket_id": 42
    }
}
```

**Error responses:**

| Status | Message |
|---|---|
| 400 | `"Cancellations are not allowed within 2 hours of departure."` |

**Flutter notes:**

- This sets the ticket status to `3` (cancelled) and creates a Refund record with status `0` (pending admin approval).
- The operation is atomic (DB transaction). The refund amount is calculated server-side.
- Display the refund tier table to users before they confirm cancellation so they understand the refund they will receive.
- After cancellation, the ticket will no longer appear in upcoming trips.

---

#### POST /ticket/{id}/rate

Rate a completed trip. Only available after the journey date has passed.

**Auth required:** Yes

**Path parameters:**

| Param | Description |
|---|---|
| `id` | Booked ticket ID |

**Request body:**

```json
{
    "rating": 5,
    "comment": "Great service! Very comfortable bus."
}
```

**Validation rules:**

| Field | Rules |
|---|---|
| `rating` | required, integer, min:1, max:5 |
| `comment` | nullable, string, max:500 |

**Response (200):**

```json
{
    "status": "success",
    "message": "Thank you for your feedback!",
    "data": {
        "id": 1,
        "booked_ticket_id": 42,
        "passenger_id": 2,
        "trip_id": 5,
        "rating": 5,
        "comment": "Great service! Very comfortable bus.",
        "created_at": "2026-03-16T10:30:00.000000Z",
        "updated_at": "2026-03-16T10:30:00.000000Z"
    }
}
```

**Error responses:**

| Status | Message |
|---|---|
| 400 | `"You can only rate a trip after the journey has started."` |
| 400 | `"You have already rated this trip."` |

**Flutter notes:**

- Show the rating prompt only for trips in the history list (past trips).
- Track locally whether a trip has been rated to avoid showing the prompt again. The 400 "already rated" error is a fallback.
- Use a star-rating widget with an optional text comment field.

---

## 7. Complete Booking Flow

The following diagram shows the full happy-path flow from search to e-ticket:

```
Step 1: Search
    GET /locations                      # Cache location list
    GET /search?pickup_id=1             # Find available trips
              &destination_id=2
              &date=2026-03-15
        |
        v
Step 2: Select Seats
    GET /trip/5/layout?date=2026-03-15  # Get seat map
                      &pickup_id=1
                      &destination_id=2
    [User selects available seats]
        |
        v
Step 3: Book
    POST /booking/initiate              # Reserve seats
    {trip_id, date, pickup_id,
     destination_id, seats,
     passenger_details}
    --> Returns: trx, amount, booking_id
        |
        v
Step 4: Pay
    GET /payment/methods                # Show payment options
    [User selects a method]
        |
        v
    POST /payment/initiate              # Start payment
    {booking_id, method_code, currency}
        |
        +---> type: "manual"
        |       |
        |       v
        |     [Show HTML instructions]
        |       |
        |       v
        |     POST /payment/manual/confirm
        |     {trx, ...proof fields}
        |       |
        |       v
        |     [Pending admin approval]
        |
        +---> type: "automatic"
                |
                v
              [Open WebView / gateway SDK]
                |
                v
              [Gateway callback confirms payment]
        |
        v
Step 5: View E-Ticket
    GET /ticket/42/view                 # Full ticket + QR data
    [Display ticket with QR code]
        |
        v
Step 6: Post-Journey
    POST /ticket/42/rate                # Rate after travel
    {rating: 5, comment: "Great!"}
```

**Cancellation flow (branches from Step 5):**

```
    GET /ticket/42/view
        |
        v
    [User taps "Cancel Ticket"]
        |
        v
    [Show refund tier information]
        |
        v
    POST /ticket/42/cancel
    --> Returns: refund_amount, refund_percent
```

---

## 8. Booking Status Codes

| Status | Meaning | Where It Appears |
|---|---|---|
| `0` | Pending (unpaid) | After `/booking/initiate`, before payment is confirmed |
| `1` | Confirmed (paid) | In upcoming trips, history, and ticket view |
| `3` | Cancelled (refund pending) | After `/ticket/{id}/cancel`; ticket removed from upcoming |

**Flutter state mapping suggestion:**

```dart
enum BookingStatus {
  pending(0, 'Pending Payment'),
  confirmed(1, 'Confirmed'),
  cancelled(3, 'Cancelled');

  const BookingStatus(this.value, this.label);
  final int value;
  final String label;

  static BookingStatus fromValue(int v) =>
      BookingStatus.values.firstWhere((e) => e.value == v);
}
```

---

## 9. Known Limitations & Workarounds

### 9.1 date_of_journey String Comparison

**Problem:** The `date_of_journey` field is stored as a `m/d/Y` format string (e.g., `"02/15/2026"`). The server-side upcoming/history queries use string comparison operators (`>=`, `<`) which perform **lexicographic** comparison, not chronological. This means `"02/15/2026" < "12/01/2025"` evaluates as true (because `"0" < "1"` lexicographically), which is incorrect.

**Impact:** Upcoming trips may include past trips, and history may include future trips, particularly across month boundaries.

**Flutter workaround:** Always re-filter results client-side by parsing `date_of_journey` into a `DateTime` object:

```dart
DateTime parseJourneyDate(String dateStr) {
  // Format: "MM/dd/yyyy"
  final parts = dateStr.split('/');
  return DateTime(
    int.parse(parts[2]), // year
    int.parse(parts[0]), // month
    int.parse(parts[1]), // day
  );
}

// Filter upcoming trips client-side
final now = DateTime.now();
final today = DateTime(now.year, now.month, now.day);

final upcomingTrips = serverTrips.where((trip) {
  final journeyDate = parseJourneyDate(trip.dateOfJourney);
  return !journeyDate.isBefore(today);
}).toList();
```

### 9.2 Automatic Payment Gateways May Return Null process_data

**Problem:** The server-side `ProcessController::process()` methods were originally designed for web browsers and may return HTML views instead of JSON. When the API layer calls `json_decode()` on HTML content, it returns `null`, so `process_data` in the response may be `null`.

**Flutter workaround:** For automatic gateways, implement a WebView-based payment flow:

```dart
if (paymentResponse.type == 'automatic') {
  if (paymentResponse.processData != null &&
      paymentResponse.processData!['redirect_url'] != null) {
    // Open in WebView or url_launcher
    await launchUrl(Uri.parse(paymentResponse.processData!['redirect_url']));
  } else {
    // Fallback: open the web payment page in a WebView
    final webPaymentUrl = '${baseUrl}/payment/process/${paymentResponse.trx}';
    // Use webview_flutter or InAppWebView
  }
}
```

### 9.3 No Pending Booking Expiry

**Problem:** Bookings with status `0` (pending/unpaid) persist indefinitely in the database. There is no automatic expiration or cleanup job.

**Impact:** This does **not** block seats for other users (seat availability only counts status `1` bookings). However, it means a user could have orphaned pending bookings.

**Flutter workaround:** This is cosmetic only. Do not display status `0` bookings in the "upcoming trips" list. If you track pending bookings locally (e.g., to allow payment retry), implement a client-side expiry (e.g., discard pending bookings older than 30 minutes).

---

## 10. Recommended Dart Models

### Generic API Response Wrapper

```dart
import 'dart:convert';

class ApiResponse<T> {
  final String status;
  final String? message;
  final T? data;

  ApiResponse({
    required this.status,
    this.message,
    this.data,
  });

  bool get isSuccess => status == 'success';

  factory ApiResponse.fromJson(
    Map<String, dynamic> json,
    T Function(dynamic)? fromJsonT,
  ) {
    return ApiResponse(
      status: json['status'] as String,
      message: json['message'] is String ? json['message'] as String : null,
      data: json['data'] != null && fromJsonT != null
          ? fromJsonT(json['data'])
          : null,
    );
  }
}
```

### API Error Model

```dart
class ApiError implements Exception {
  final int statusCode;
  final String? remark;
  final List<String> errors;

  ApiError({
    required this.statusCode,
    this.remark,
    required this.errors,
  });

  String get firstError => errors.isNotEmpty ? errors.first : 'Unknown error';

  bool get isUnauthenticated => statusCode == 401;
  bool get isForbidden => statusCode == 403;
  bool get isRateLimited => statusCode == 429;
  bool get isValidationError => statusCode == 422;

  factory ApiError.fromResponse(int statusCode, Map<String, dynamic> json) {
    final message = json['message'];
    List<String> errors = [];

    if (message is Map) {
      final errorList = message['error'];
      if (errorList is List) {
        errors = errorList.cast<String>();
      }
    } else if (message is String) {
      errors = [message];
    }

    return ApiError(
      statusCode: statusCode,
      remark: json['remark'] as String?,
      errors: errors,
    );
  }

  @override
  String toString() => 'ApiError($statusCode): $firstError';
}
```

### Passenger Model

```dart
class Passenger {
  final int id;
  final String firstname;
  final String lastname;
  final String email;
  final String dialCode;
  final String mobile;
  final String? image;
  final int status;
  final int ev;
  final int sv;
  final int? profileComplete;
  final String? otpExpiresAt;
  final String createdAt;

  Passenger({
    required this.id,
    required this.firstname,
    required this.lastname,
    required this.email,
    required this.dialCode,
    required this.mobile,
    this.image,
    required this.status,
    required this.ev,
    required this.sv,
    this.profileComplete,
    this.otpExpiresAt,
    required this.createdAt,
  });

  String get fullName => '$firstname $lastname';
  bool get isPhoneVerified => sv == 1;
  bool get isEmailVerified => ev == 1;
  bool get isActive => status == 1;

  factory Passenger.fromJson(Map<String, dynamic> json) {
    return Passenger(
      id: json['id'] as int,
      firstname: json['firstname'] as String,
      lastname: json['lastname'] as String,
      email: json['email'] as String,
      dialCode: json['dial_code'] as String,
      mobile: json['mobile'] as String,
      image: json['image'] as String?,
      status: json['status'] as int,
      ev: json['ev'] as int,
      sv: json['sv'] as int,
      profileComplete: json['profile_complete'] as int?,
      otpExpiresAt: json['otp_expires_at'] as String?,
      createdAt: json['created_at'] as String,
    );
  }

  Map<String, dynamic> toJson() => {
        'id': id,
        'firstname': firstname,
        'lastname': lastname,
        'email': email,
        'dial_code': dialCode,
        'mobile': mobile,
        'image': image,
        'status': status,
        'ev': ev,
        'sv': sv,
        'profile_complete': profileComplete,
        'otp_expires_at': otpExpiresAt,
        'created_at': createdAt,
      };
}
```

### Auth Response Model

```dart
class AuthResponse {
  final String token;
  final Passenger passenger;

  AuthResponse({required this.token, required this.passenger});

  factory AuthResponse.fromJson(Map<String, dynamic> json) {
    return AuthResponse(
      token: json['token'] as String,
      passenger: Passenger.fromJson(json['passenger'] as Map<String, dynamic>),
    );
  }
}
```

### Location Model

```dart
class Location {
  final int id;
  final String name;
  final String location;

  Location({required this.id, required this.name, required this.location});

  String get displayName => '$name ($location)';

  factory Location.fromJson(Map<String, dynamic> json) {
    return Location(
      id: json['id'] as int,
      name: json['name'] as String,
      location: json['location'] as String,
    );
  }
}
```

### Trip Search Result Model

```dart
class TripSearchResult {
  final int tripId;
  final String ownerName;
  final String busType;
  final String departureTime;
  final String arrivalTime;
  final num fare;
  final int availableSeats;
  final String routeName;

  TripSearchResult({
    required this.tripId,
    required this.ownerName,
    required this.busType,
    required this.departureTime,
    required this.arrivalTime,
    required this.fare,
    required this.availableSeats,
    required this.routeName,
  });

  factory TripSearchResult.fromJson(Map<String, dynamic> json) {
    return TripSearchResult(
      tripId: json['trip_id'] as int,
      ownerName: json['owner_name'] as String,
      busType: json['bus_type'] as String,
      departureTime: json['departure_time'] as String,
      arrivalTime: json['arrival_time'] as String,
      fare: json['fare'] as num,
      availableSeats: json['available_seats'] as int,
      routeName: json['route_name'] as String,
    );
  }
}
```

### Booked Ticket Model

```dart
class BookedTicket {
  final int id;
  final int ownerId;
  final int tripId;
  final int passengerId;
  final List<dynamic> sourceDestination;
  final int pickUpPoint;
  final int droppingPoint;
  final List<String> seats;
  final List<PassengerDetail> passengerDetails;
  final int ticketCount;
  final String dateOfJourney;
  final int status;
  final String trx;
  final num price;
  final String createdAt;
  final String updatedAt;
  final TripInfo? trip;

  BookedTicket({
    required this.id,
    required this.ownerId,
    required this.tripId,
    required this.passengerId,
    required this.sourceDestination,
    required this.pickUpPoint,
    required this.droppingPoint,
    required this.seats,
    required this.passengerDetails,
    required this.ticketCount,
    required this.dateOfJourney,
    required this.status,
    required this.trx,
    required this.price,
    required this.createdAt,
    required this.updatedAt,
    this.trip,
  });

  BookingStatus get bookingStatus => BookingStatus.fromValue(status);

  /// Parse the m/d/Y date string into a DateTime for reliable comparison.
  DateTime get journeyDate {
    final parts = dateOfJourney.split('/');
    return DateTime(
      int.parse(parts[2]),
      int.parse(parts[0]),
      int.parse(parts[1]),
    );
  }

  factory BookedTicket.fromJson(Map<String, dynamic> json) {
    return BookedTicket(
      id: json['id'] as int,
      ownerId: json['owner_id'] as int,
      tripId: json['trip_id'] as int,
      passengerId: json['passenger_id'] as int,
      sourceDestination: json['source_destination'] as List<dynamic>,
      pickUpPoint: json['pick_up_point'] as int,
      droppingPoint: json['dropping_point'] as int,
      seats: (json['seats'] as List<dynamic>).cast<String>(),
      passengerDetails: (json['passenger_details'] as List<dynamic>)
          .map((e) => PassengerDetail.fromJson(e as Map<String, dynamic>))
          .toList(),
      ticketCount: json['ticket_count'] as int,
      dateOfJourney: json['date_of_journey'] as String,
      status: json['status'] as int,
      trx: json['trx'] as String,
      price: json['price'] as num,
      createdAt: json['created_at'] as String,
      updatedAt: json['updated_at'] as String,
      trip: json['trip'] != null
          ? TripInfo.fromJson(json['trip'] as Map<String, dynamic>)
          : null,
    );
  }
}

class PassengerDetail {
  final String name;
  final String? mobile;
  final String? gender;

  PassengerDetail({required this.name, this.mobile, this.gender});

  factory PassengerDetail.fromJson(Map<String, dynamic> json) {
    return PassengerDetail(
      name: json['name'] as String,
      mobile: json['mobile'] as String?,
      gender: json['gender'] as String?,
    );
  }

  Map<String, dynamic> toJson() => {
        'name': name,
        if (mobile != null) 'mobile': mobile,
        if (gender != null) 'gender': gender,
      };
}
```

### Trip Info Model (nested in tickets)

```dart
class TripInfo {
  final int id;
  final String title;
  final int? ownerId;
  final OwnerInfo? owner;
  final RouteInfo? route;
  final ScheduleInfo? schedule;
  final FleetTypeInfo? fleetType;

  TripInfo({
    required this.id,
    required this.title,
    this.ownerId,
    this.owner,
    this.route,
    this.schedule,
    this.fleetType,
  });

  factory TripInfo.fromJson(Map<String, dynamic> json) {
    return TripInfo(
      id: json['id'] as int,
      title: json['title'] as String,
      ownerId: json['owner_id'] as int?,
      owner: json['owner'] != null
          ? OwnerInfo.fromJson(json['owner'] as Map<String, dynamic>)
          : null,
      route: json['route'] != null
          ? RouteInfo.fromJson(json['route'] as Map<String, dynamic>)
          : null,
      schedule: json['schedule'] != null
          ? ScheduleInfo.fromJson(json['schedule'] as Map<String, dynamic>)
          : null,
      fleetType: json['fleetType'] != null
          ? FleetTypeInfo.fromJson(json['fleetType'] as Map<String, dynamic>)
          : null,
    );
  }
}

class OwnerInfo {
  final int id;
  final String lastname;

  OwnerInfo({required this.id, required this.lastname});

  factory OwnerInfo.fromJson(Map<String, dynamic> json) {
    return OwnerInfo(
      id: json['id'] as int,
      lastname: json['lastname'] as String,
    );
  }
}

class RouteInfo {
  final int id;
  final String name;
  final List<String>? stoppages;

  RouteInfo({required this.id, required this.name, this.stoppages});

  factory RouteInfo.fromJson(Map<String, dynamic> json) {
    return RouteInfo(
      id: json['id'] as int,
      name: json['name'] as String,
      stoppages: json['stoppages'] != null
          ? (json['stoppages'] as List<dynamic>).cast<String>()
          : null,
    );
  }
}

class ScheduleInfo {
  final int id;
  final String startFrom;
  final String endAt;

  ScheduleInfo({required this.id, required this.startFrom, required this.endAt});

  factory ScheduleInfo.fromJson(Map<String, dynamic> json) {
    return ScheduleInfo(
      id: json['id'] as int,
      startFrom: json['start_from'] as String,
      endAt: json['end_at'] as String,
    );
  }
}

class FleetTypeInfo {
  final int id;
  final String name;

  FleetTypeInfo({required this.id, required this.name});

  factory FleetTypeInfo.fromJson(Map<String, dynamic> json) {
    return FleetTypeInfo(
      id: json['id'] as int,
      name: json['name'] as String,
    );
  }
}
```

### QR Data Model

```dart
class QrData {
  final String trx;
  final String passenger;
  final String bus;
  final List<String> seats;
  final String date;

  QrData({
    required this.trx,
    required this.passenger,
    required this.bus,
    required this.seats,
    required this.date,
  });

  factory QrData.fromJson(Map<String, dynamic> json) {
    return QrData(
      trx: json['trx'] as String,
      passenger: json['passenger'] as String,
      bus: json['bus'] as String,
      seats: (json['seats'] as List<dynamic>).cast<String>(),
      date: json['date'] as String,
    );
  }

  /// Encode as JSON string for QR code generation.
  String toQrString() => jsonEncode({
        'trx': trx,
        'passenger': passenger,
        'bus': bus,
        'seats': seats,
        'date': date,
      });
}
```

### Paginated Response Model

```dart
class PaginatedResponse<T> {
  final int currentPage;
  final List<T> data;
  final int lastPage;
  final int perPage;
  final int total;
  final String? nextPageUrl;
  final String? prevPageUrl;

  PaginatedResponse({
    required this.currentPage,
    required this.data,
    required this.lastPage,
    required this.perPage,
    required this.total,
    this.nextPageUrl,
    this.prevPageUrl,
  });

  bool get hasNextPage => nextPageUrl != null;
  bool get hasPrevPage => prevPageUrl != null;

  factory PaginatedResponse.fromJson(
    Map<String, dynamic> json,
    T Function(Map<String, dynamic>) fromJsonT,
  ) {
    return PaginatedResponse(
      currentPage: json['current_page'] as int,
      data: (json['data'] as List<dynamic>)
          .map((e) => fromJsonT(e as Map<String, dynamic>))
          .toList(),
      lastPage: json['last_page'] as int,
      perPage: json['per_page'] as int,
      total: json['total'] as int,
      nextPageUrl: json['next_page_url'] as String?,
      prevPageUrl: json['prev_page_url'] as String?,
    );
  }
}
```

---

## 11. Flutter Architecture Recommendations

### Recommended Packages

| Package | Purpose | Version Guidance |
|---|---|---|
| `dio` | HTTP client with interceptors | Latest stable |
| `flutter_secure_storage` | Secure token storage | Latest stable |
| `json_serializable` or `freezed` | Type-safe JSON parsing (alternative to manual models above) | Latest stable |
| `qr_flutter` | QR code rendering for e-tickets | Latest stable |
| `flutter_html` | Render HTML payment instructions | Latest stable |
| `webview_flutter` | Automatic gateway payment flow | Latest stable |
| `url_launcher` | Fallback for external payment URLs | Latest stable |
| `connectivity_plus` | Network state detection | Latest stable |

### Dio Interceptor Setup

```dart
import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class ApiClient {
  static const String baseUrl = 'https://your-domain.com/api/v1';

  final Dio _dio;
  final FlutterSecureStorage _storage;

  ApiClient({FlutterSecureStorage? storage})
      : _storage = storage ?? const FlutterSecureStorage(),
        _dio = Dio(BaseOptions(
          baseUrl: baseUrl,
          connectTimeout: const Duration(seconds: 30),
          receiveTimeout: const Duration(seconds: 30),
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
          },
        )) {
    _dio.interceptors.add(_AuthInterceptor(_storage));
    _dio.interceptors.add(_ErrorInterceptor());
  }

  Dio get dio => _dio;
}

class _AuthInterceptor extends Interceptor {
  final FlutterSecureStorage _storage;

  _AuthInterceptor(this._storage);

  @override
  void onRequest(
    RequestOptions options,
    RequestInterceptorHandler handler,
  ) async {
    final token = await _storage.read(key: 'auth_token');
    if (token != null) {
      options.headers['Authorization'] = 'Bearer $token';
    }
    handler.next(options);
  }
}

class _ErrorInterceptor extends Interceptor {
  @override
  void onError(DioException err, ErrorInterceptorHandler handler) {
    if (err.response != null) {
      final data = err.response!.data;
      if (data is Map<String, dynamic>) {
        final apiError = ApiError.fromResponse(
          err.response!.statusCode ?? 500,
          data,
        );

        // Handle 401 globally (e.g., redirect to login)
        if (apiError.isUnauthenticated) {
          // Clear token and navigate to login
          // This depends on your navigation setup
        }

        handler.reject(DioException(
          requestOptions: err.requestOptions,
          response: err.response,
          error: apiError,
        ));
        return;
      }
    }
    handler.next(err);
  }
}
```

### Token Storage Pattern

```dart
class AuthService {
  final FlutterSecureStorage _storage;

  AuthService({FlutterSecureStorage? storage})
      : _storage = storage ?? const FlutterSecureStorage();

  Future<void> saveToken(String token) async {
    await _storage.write(key: 'auth_token', value: token);
  }

  Future<String?> getToken() async {
    return await _storage.read(key: 'auth_token');
  }

  Future<void> clearToken() async {
    await _storage.delete(key: 'auth_token');
  }

  Future<bool> isAuthenticated() async {
    final token = await getToken();
    return token != null && token.isNotEmpty;
  }
}
```

### Offline Caching Strategy

```dart
// Cache upcoming trips for instant display on app launch.
// Re-fetch from API in the background and update the UI.
//
// Recommended approach:
// 1. On successful GET /passenger/trips/upcoming, serialize and store
//    the response in SharedPreferences or a local database (sqflite, Hive).
// 2. On app launch, immediately display cached data.
// 3. Fetch fresh data from the API in parallel.
// 4. Replace cached data with fresh data once the API responds.
// 5. If the API call fails (no network), continue showing cached data
//    with a "Last updated X minutes ago" indicator.
```

### Error Handling Pattern

```dart
Future<void> handleApiCall(Future<void> Function() apiCall) async {
  try {
    await apiCall();
  } on DioException catch (e) {
    if (e.error is ApiError) {
      final apiError = e.error as ApiError;

      if (apiError.isRateLimited) {
        showSnackBar('Please wait a moment before trying again.');
      } else if (apiError.isUnauthenticated) {
        // Already handled globally by interceptor
      } else if (apiError.isForbidden) {
        showBannedAccountScreen();
      } else if (apiError.isValidationError) {
        showValidationErrors(apiError.errors);
      } else {
        showSnackBar(apiError.firstError);
      }
    } else if (e.type == DioExceptionType.connectionTimeout ||
               e.type == DioExceptionType.receiveTimeout) {
      showSnackBar('Connection timed out. Please check your internet.');
    } else {
      showSnackBar('Something went wrong. Please try again.');
    }
  }
}
```

---

**End of guide.** For questions about the API, contact the backend team. For the latest endpoint changes, refer to the Laravel route files at `routes/api.php`.
