# Phase 3: Mobile Payment Gateway Integration (Execution Plan)

This phase connects the passenger's wallet to the TransLab booking engine through both global (Stripe, Razorpay) and local (SyberPay, BOK) channels.

---

## 1. Unified Payment API
**Objective**: A single endpoint that starts the payment process regardless of the chosen gateway.

### Technical Tasks:
1.  **API Endpoint**: `POST /api/v1/payment/initiate`.
2.  **Parameters**: `booking_id`, `gateway_alias`, `callback_url`.
3.  **Logic**:
    - Validate the booking (`status = 0`).
    - Fetch the Gateway configuration.
    - Generate a standard "Checkout Response" (Redirect URL or Payment Intent).

---

## 2. Sudanese Local Gateways (SyberPay/BOK)
**Objective**: Prioritize Sudanese accessibility using the roadmap's regional strategy.

### Technical Tasks:
1.  **SyberPay Bridge**: Adapt the existing manual/web payment logic into an API-friendly format for Flutter.
2.  **BOK Manual Upload**: Allow passengers to upload a screenshot of their result from the Bank of Khartoum app for Admin approval.
3.  **Offline-Friendly**: Clearly display the total cost in SDG and provide instructions for manual transfers if the internet is unstable.

---

## 3. Webhook & Fulfillment
**Objective**: Automatically turn "Pending" seats into "Sold" seats once the money is received.

### Technical Tasks:
1.  **Route**: `/api/v1/payment/callback/{trx}`.
2.  **Fulfillment Logic**:
    - Verify gateway signature/status.
    - Update `booked_tickets` status to `1` (Sold).
    - Generate `Transaction` record for accounting.
    - Trigger "Ticket Confirmed" notification.

---

## 4. Phase 3 Acceptance Criteria
- [ ] Flutter app can request a "Payment URL" for a specific seat booking.
- [ ] Successfully completing a test payment (e.g., Stripe Test) automatically updates the seat status in the database.
- [ ] Passenger receives a "Success" JSON response after verification.
- [ ] BOK "Manual Receipt" flow works for users without credit cards.

---

## Sudan Regional Context
> [!IMPORTANT]
> Since Sudanese credit card infrastructure can be intermittent, the BOK (Bank of Khartoum) manual flow is the **safety net** that ensures 100% of users can book tickets even during sanctions or network dips.
