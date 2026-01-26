# System Process Gap Analysis Report

**Date:** January 20, 2026
**Project:** Student Placement Attachment System
**Scope:** Comparison of implemented codebase against provided Process Design specifications.

---

## 1. Executive Summary
The current system successfully implements the core logic for User Authentication, Opportunity Management, and Student Application/Clearance. However, there are significant deviations from the process design regarding **Payment Processing**, **Supervision Structure** (Dual Supervisors), and **Assessment Cycles** (Multiple Assessments).

---

## 2. Detailed Verification

### 2.1 User Authentication
*   **Requirement:** Admin logging, System authentication, Decision point for valid credentials.
*   **Status:** ✅ **Implemented**
*   **Notes:** Distinct login portals exist for Students, Staff, Host Org, and Admin.

### 2.2 Student Process
*   **Requirement:** View Opportunities -> Submit Application -> Eligibility Check -> Accepted/Rejected.
*   **Status:** ✅ **Implemented**
    *   `student-opportunities.php` lists active opportunities.
    *   `student-applications.php` handles application submission.
    *   New "Program Applications" feature in Admin dashboard handles the eligibility approval loop.

### 2.3 Student Registration & Payment
*   **Requirement:** "Student registers... -> **pays the required fees -> payment confirmed** -> Online Logbook Issued"
*   **Status:** 🔴 **Critical Gap**
    *   **Finding:** The system has **NO** payment processing module, database tables for fee tracking, or payment confirmation logic.
    *   **Current Behavior:** Students are cleared directly by the Admin and immediately receive access to the Logbook features.
    *   **Recommendation:** Implement a `payments` table and a "Finance/Bursar" role or automated mock payment gateway.

### 2.4 Supervision Structure
*   **Requirement:** "Admin allocates **two supervisors**: One technical supervisor (Host Org) and One non-technical supervisor (Academic)."
*   **Status:** 🟡 **Partial Implementation**
    *   **Finding:** The `supervision` table links a `LecturerID` to an `AttachmentID`. This covers the "Non-technical/Academic" supervisor.
    *   **Gap:** There is no explicit field or table to assign a specific "Technical Supervisor" at the Host Organization. The link implies the entire Host Org is the supervisor.
    *   **Recommendation:** Add `TechnicalSupervisorID` to the `supervision` or `attachment` table, linking to a specific Host Org representative.

### 2.5 Assessment Cycle
*   **Requirement:** "Assessment takes place (**loop x2** since assessment is done twice) -> Grades are issued."
*   **Status:** 🟡 **Partial Implementation**
    *   **Finding:** `staff-assessment.php` allows submitting an assessment.
    *   **Gap:** The system treats assessment as a single event. There is no distinction between "First Assessment" and "Final Assessment". Submitting a second one would likely overwrite the first or be ambiguous.
    *   **Recommendation:** Add an `AssessmentType` (e.g., 'Mid-Placement', 'Final') to the `assessments` table to support the x2 loop.

### 2.6 Host Organization Logbook Feedback
*   **Requirement:** Host Org reviews logbook -> "Is feedback required?" -> Submit structured feedback.
*   **Status:** 🟡 **Partial Implementation**
    *   **Finding:** `student-logbook.php` allows students to enter data.
    *   **Gap:** While Host Orgs have a dashboard, the specific workflow for granular "per-entry" feedback or "weekly approval" needs verification of its depth.
    *   **Recommendation:** Ensure `host-org-dashboard.php` has a dedicated "Logbook Review" view.

---

## 3. Recommended Roadmap

To align the system with the Process Design, the following development tasks are prioritized:

1.  **Phase 1: Payment Module (High Priority)**
    *   Create `payments` database table.
    *   Create "Fee Payment" page for students.
    *   Add "Payment Confirmation" step to Admin clearance workflow.

2.  **Phase 2: Advanced Supervision (Medium Priority)**
    *   Update database to support assigning a Technical Supervisor.
    *   Update `admin-supervisors.php` to allow dual assignment.

3.  **Phase 3: Multi-Stage Assessment (Medium Priority)**
    *   Update `assessments` table to include `AssessmentStage` (1 or 2).
    *   Update `staff-assessment.php` logic to support multiple submissions.
