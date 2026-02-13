# Operator Panel Audit Report - TransLab System

**Audit Date:** 2026-02-13  
**Auditor:** AI Agent  
**System:** TransLab Transport Ticket Booking System  
**Scope:** Entire Operator (Owner) Panel  

---

## Executive Summary

[Summary will be added after audit completion]

**Key Findings:**
- **Critical Issues:** [TBD]
- **Major Issues:** [TBD]
- **Minor Issues:** [TBD]
- **Positive Aspects:** [TBD]

**Overall Robustness Score:** [TBD]/10

---

# Operator Panel Audit Report for TransLab

## 1. Executive Summary

This report outlines the findings from a comprehensive code audit of the TransLab Operator Panel. The audit aimed to identify security vulnerabilities, logical flaws, and areas for improvement in robustness and user experience.

**Key Findings:**
*   **Critical Security Vulnerabilities (IDOR):** Found in the `Crud` trait and `GlobalStatus` trait, affecting staff management and status toggling for multiple core entities (Vehicles, Trips, etc.). These allow unauthorized modification of data belonging to other operators.
*   **Validation Gaps:** Missing ownership checks in `VehicleTicketController` (pricing) and `BoardingPointController` (route assignment) allow operators to assign or price assets they do not own.
*   **UX/UI Inconsistencies:** While generally clean, some forms (Trip Wizard) lack immediate feedback, and others (Vehicle Create) are quite long and effectively split between modal and full-page without clear direction.

## 2. Functional Area: Fleet Management

### 2.1. Critical & High Severity Issues
*   **Vulnerability (High):** `FleetController::changeVehicleStatus` uses the `GlobalStatus` trait, which lacks ownership validation. An attacker can toggle the active status of *any* vehicle in the system by guessing its ID.
    *   **File:** `core/app/Http/Controllers/Owner/FleetController.php`, `core/app/Traits/GlobalStatus.php`
    *   **Remediation:** Override `changeStatus` in the controller or modify `GlobalStatus` to accept a scope/owner check.

### 2.2. Medium & Low Severity Issues
*   **Data Integrity (Medium):** The `vehicle-select` in Trip Wizard loads all vehicles for the owner but relies on frontend logic to filter by Fleet Type. Backend does not strictly enforce that `vehicle_id` matches the `fleet_type_id` of the trip during `store`.
*   **UX (Low):** `VehicleTicketController` uses a modal-like view for creating prices but lacks client-side validation feedback (no red borders, only toast notifications).

## 3. Functional Area: Trip Management

### 3.1. Critical & High Severity Issues
*   **Vulnerability (High):** `TripController::changeStatus` also uses `GlobalStatus` trait, allowing unauthorized status toggling of trips.

### 3.2. Medium & Low Severity Issues
*   **Logic (Medium):** `assignVehicleToTrip` method checks for conflicts but assumes the operator has the right to assign the vehicle. While `vehicle_id` validation ensures existence, it should explicitly check `owner_id` ownership of the vehicle being assigned.
*   **Fragility (Medium):** The `bookedTickets` loop in `BookingController::manage` logic could potentially lead to N+1 performance issues if passenger details were essentially relational (though they seem to be JSON currently).

## 4. Functional Area: Booking & Operations

### 4.1. Critical & High Severity Issues
*   **Vulnerability (High):** `VehicleTicketController::ticketPriceStore` validates the existence of `route_id` and `fleet_type_id` but **does not check if they belong to the authenticated owner**. A malicious owner could set ticket prices for another owner's route.
    *   **File:** `core/app/Http/Controllers/Owner/VehicleTicketController.php`
    *   **Remediation:** Add `where('owner_id', $user->id)` checks for `route` and `fleet_type` before saving.
*   **Vulnerability (High):** `BoardingPointController::assignStore` allows assigning *any* valid boarding point ID to a route, without checking if the boarding point belongs to the owner.
    *   **File:** `core/app/Http/Controllers/Owner/BoardingPointController.php`

## 5. Functional Area: HR & Access Control

### 5.1. Critical & High Severity Issues
*   **Vulnerability (Critical - IDOR):** The `Crud` trait used by `SupervisorController` and `CoOwnerController` (and potentially others) has a critical flaw in `form($id)` and `store($id)`. It uses `model::findOrFail($id)` without scoping by `owner_id`.
    *   **Impact:** An operator can View (PII leak), Edit, and Hijack the account of another operator's staff member.
    *   **File:** `core/app/Traits/Crud.php`
    *   **Remediation:** Modify `Crud` trait to use `model::where('owner_id', $this->owner->id)->findOrFail($id)`.

## 6. Strategic Improvement Plan

### 6.1. Immediate Remediation (Security Hotfixes)
1.  **Patch `Crud` Trait:** Enforce `owner_id` check in `form()` and `store()` methods immediately.
2.  **Patch `GlobalStatus` Trait:** Add an optional `$ownerId` parameter or check for `owner_id` column existence and enforce authentication check.
3.  **Secure Assignments:** Update `VehicleTicketController` and `BoardingPointController` to validate ownership of related entities (Routes, FleetTypes, BoardingPoints).

### 6.2. Reliability Enhancements
1.  **Strict Validation:** Implement a `BelongsToOwner` validation rule and apply it to all entity references in requests (Vehicle ID, Route ID, Schedule ID).
2.  **Frontend Hardening:** Ensure all dropdowns and selectors in the frontend (like Trip Wizard) physically filter options based on dependencies (e.g., only show vehicles matching the selected Fleet Type).

### 6.3. Future Roadmap
1.  **Unified Permission System:** Move away from ad-hoc JSON permissions in `DriverController` to a dedicated Role-Permission structure (spatie/laravel-permission) for better scalability.
2.  **Service Layer Pattern:** Refactor heavy controller logic (like in `TripController`) into dedicated Services (`TripService`, `BookingService`) to isolate business logic and make testing easier.

---
**Audit Status:** Complete
**Date:** 2026-02-13

## 5. Finance & Reporting Audit
*Focus: Reports, Withdrawals, Financial Logs*

### 5.1 Backend Analysis
[Findings]

### 5.2 Frontend Analysis
[Findings]

---

## 6. Strategic Improvement Plan

### 6.1 Critical Fixes (Immediate)
| # | Recommendation | Location | Impact |
|---|----------------|----------|--------|
| | | | |

### 6.2 Major Improvements (Short Term)
| # | Recommendation | Location | Impact |
|---|----------------|----------|--------|
| | | | |

---

## 7. Detailed Code Remediation

[Specific code examples for fixing identified issues]
