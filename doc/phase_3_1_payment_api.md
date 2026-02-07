# Phase 3.1: Mobile Payment Gateway Integration Spec

## 1. Overview
This phase provides the bridge between the internal TransLab payment logic and the Flutter mobile app. Since most gateways (Stripe, Razorpay, etc.) provide dedicated Mobile SDKs, our API must provide the "Handshake Data" required to trigger those SDKs.

---

## 2. The API-ified Payment Workflow
For B2C, we will reuse the `Deposit` model but redirect its output to the mobile app.

### 2.1 Get Active Gateways
- **Endpoint**: `GET /api/v1/payment/gateways`
- **Response**: List of enabled gateway names, logos, currencies, and their `method_code`.

### 2.2 Payment Initialization
- **Endpoint**: `POST /api/v1/payment/initiate`
- **Authentication**: Required.
- **Body**: `booking_id`, `method_code`, `currency`.
- **Logic**: 
  1. Calculate `final_amount` and `charge` (Reuse `PaymentController::depositInsert`).
  2. Create a `Deposit` record linked to the pending booking.
  3. Call the gateway's `ProcessController`.

---

## 3. Gateway Data Mapping (The "Handshake")
Instead of returning a Blade view, the API will return a specialized JSON block per gateway.

| Gateway | What the API Returns | Flutter Implementation |
| :--- | :--- | :--- |
| **SyberPay** | `payment_url` or `reference`. | Use `url_launcher` for the Sudanese local gateway. |
| **Stripe** | `client_secret` (Payment Intent). | Use `flutter_stripe` package to collect card. |
| **Razorpay** | `order_id` and `amount`. | Use `razorpay_flutter` to open native dialog. |
| **BOK/USSD** | `ussd_code` or `phone_number`.| Manual confirmation flow for Bank of Khartoum transfers. |

---

## 4. Webhook & Instant Payment Notification (IPN)
- **Role**: Essential for reliability. In Sudan, user internet might drop mid-payment. The **Webhook/IPN** is the primary source of truth for booking success.
- **Action**: Adapt existing `ipn.php` routes to update the `booked_tickets` status to `1` (Active).

---

## 5. Completion Criteria
1. **Gateway List API**: Correctly lists enabled methods.
2. **Dynamic JSON Response**: The `initiate` endpoint returns SDK-specific data instead of HTML.
3. **Status Sync**: A successful payment via a mobile SDK is correctly reflected in the Admin and Owner dashboards.
4. **Resiliency**: Webhooks successfully confirm bookings even if the mobile app process is killed.
