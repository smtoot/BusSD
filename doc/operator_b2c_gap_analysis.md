# B2C Operator Gap Analysis (Operational Deep Dive)

While the B2C "Engine" is now complete (Search -> Book -> Pay), our deep dive into the **Ground Operations** (Bus Operators, Drivers, Supervisors) reveals critical friction points for managing these new digital passengers.

---

## 1. Ground Staff Gaps (Drivers & Supervisors)

Currently, drivers and supervisors use a mobile-friendly "Seat Plan" view. However, it lacks the necessary information to handle B2C passengers.

### [CRITICAL] Gap 1: Absence of Passenger Manifest
- **Current State**: staff can only see which seats are booked by hovering/waiting on tooltips in a visual seat chart.
- **Operational Risk**: Drivers cannot quickly see a list of names and phone numbers for the journey. This makes it impossible to check if everyone has arrived before departure.
- **B2C Impact**: App passengers expect a professional "check-in" experience; a visual-only chart is insufficient for high-volume routes.

### [IMPORTANT] Gap 2: Indistinguishable "App" Passengers
- **Current State**: All booked tickets look the same in the seat plan.
- **Operational Risk**: Ground staff don't know who has already paid via the app vs. who might still need to pay at the bus (Counter bookings).
- **Proposed Fix**: Mark App passengers with a "Smartphone" icon or a distinct color (e.g., Azure Blue).

### [CRITICAL] Gap 3: Missing QR Verification Interface
- **Current State**: We implemented QR generation for B2C tickets, but Ground Staff has **no way to scan them**.
- **Operational Risk**: Increased risk of ticket fraud or passenger confusion.

### [LOW] Gap 4: Zero Communication (Click-to-Call)
- **Current State**: Phone numbers are hidden in tooltips or nested details.
- **Proposed Fix**: Add `tel:` links next to passenger names in the manifest so drivers can call late passengers with one click.

---

## 2. Operator Management Gaps (Owners)

### Gap 5: No Inventory Control for B2C
- **Current State**: The whole bus is open for B2C search.
- **Risk**: Operators often want to reserve specific seats (e.g., front seats) for physical counter sales or VIPs.
- **B2C Impact**: We need a way for Operators to "Block" specific seats from the App while keeping them open for the Counter.

### Gap 6: Real-time Notification
- **Current State**: Operators only see B2C sales if they check the report.
- **Proposed Fix**: Implement an "Order Notification" badge in the Owner dashboard for new digital bookings.

---

## 3. Phased Operational Roadmap

To ensure a smooth transition, we have divided the "Operational Excellence" pillar into three targeted sub-phases:

| Phase | Goal | Key Features |
| :--- | :--- | :--- |
| **10.1: Ground Recognition** | Immediate Visibility | Unified Manifest list, B2C Icons, Click-to-Call. |
| **10.2: Digital Trust** | Security & Verification | QR Scanning Portal, Boarding Status (Boarded/Absent). |
| **10.3: Control & Alerts** | Operator Awareness | Seat Blocking (Counter vs App), Real-time Order Alerts. |

---

**Status**: Ready for Phase 10.1 Implementation.
