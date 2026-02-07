# Phase 1.1: Passenger Model Implementation Spec

## 1. Overview
The goal of this phase is to establish a dedicated "Passenger" entity in the system. This entity will allow individual customers to create accounts, log in via the mobile app, and track their booking history.

---

## 2. Updated Database Schema (`passengers` table)
Incorporating **Soft Deletes** and standardized fields for **Phone OTP**.

| Column | Type | Description |
| :--- | :--- | :--- |
| `id` | `bigint (PK)` | - |
| `phone_otp` | `varchar(10)` | Latest OTP code for verification. |
| `otp_expires_at`| `datetime` | Expiration for the security code. |
| `deleted_at` | `timestamp` | For **Soft Deletes** support. |

---

## 3. Model Logic & Relationships
- **Soft Deletes**: Use `Illuminate\Database\Eloquent\SoftDeletes` trait.
- **Push Notification Pattern**: 
  - We will use a separate `DeviceToken` relationship. 
  - **Best Practice**: A `Passenger` hasMany `DeviceTokens`. This allows the same user to receive notifications on their iPhone and iPad simultaneously.

---

## 4. Finalized Design Decisions
Based on your feedback:
- **Phone OTP**: User registration and critical actions will be secured via OTP. 
  - **Sudan Context**: Must integrate with a local provider (e.g., **SmsGate.io** or direct carrier APIs for Zain/MTN/Sudani) to ensure delivery during local network fluctuations.
- **Soft Deletes**: No physical deletion of user accounts. This ensures that historical booking records always have a linked (even if "deleted") user for accounting.
- **Multi-Device Support**: Fully supported via a one-to-many relationship with Device Tokens.

---

## 5. Completion Criteria (Definition of Done)
1. **Migration Executed**: `passengers` table exists with `deleted_at`.
2. **Model Created**: `Passenger` model supports Soft Deletes and multi-auth.
3. **Multi-Device Logic**: `DeviceToken` relationship is established and tested.
4. **OTP Foundation**: Fields for `phone_otp` are present in the table.
