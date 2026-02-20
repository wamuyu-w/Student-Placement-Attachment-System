# Business Logic Flows

This document details the core workflows and business rules currently identified in the Student Placement Attachment System.

## 1. User Account & Role Management Flow
- **Registration**: Users register or are added by an Admin, creating a record in the `users` table with a specific `Role` (`Student`, `Lecturer`, `Admin`, `Host Organization`). 
- **Profiles**: Based on the role, the system creates a corresponding profile record in specific tables (`student`, `lecturer`, `hostorganization`), linked via `UserID`.
- **Status Checks**: Users have an `Active` or `Inactive` status. When students complete their attachment and submit their final report, their user record status transitions to `Inactive` to restrict future logins.
- **Host Org First-time Login**: New Host Organizations are forced to update default passwords and profile details on their initial login to become active and eligible.

## 2. Attachment Application & Placement Flow
- **Opportunities**: Host Organizations create placement ads in the `attachmentopportunity` table. These have defined application periods and descriptions.
- **Student Applications**: Students can browse open opportunities and apply. Student applications are tracked in either `attachmentapplication` (general approvals for attachment eligibility) and/or `jobapplication` (specific applications to opportunities).
- **Placement Execution**: Once matched/approved, a record in the `attachment` table is generated. This links the `StudentID` with a `HostOrgID` and defines the `StartDate` and `EndDate`. The attachment transitions through states like 'Ongoing', 'Active', or 'Completed'.

## 3. Supervision & Monitoring Flow
- **Assignment**: Admins or the system assigns a `Lecturer` to monitor a student's attachment by creating a record in the `supervision` table that links `LecturerID` to the specific `AttachmentID`.
- **Oversight**: A Lecturer can thus view a list of specifically assigned "monitored students" by referencing the `supervision` table, excluding other unassigned students.

## 4. Logbook & Activity Tracking Flow
- **Issuance**: When an attachment begins, a `logbook` record is issued and linked to the `AttachmentID`.
- **Entries**: Students document their daily/weekly progress via the `logbookentry` table, which holds `EntryDate`, `Activities`, and `HostSupervisorComments`.
- **Review**: The system flags logbooks as 'Pending' if there are unreviewed entries. Supervisors (Host or Lecturer) can review and provide feedback on these entries. For comprehensive monitoring, entries can be grouped by weeks based on the `EntryDate` relative to the attachment `StartDate`.

## 5. Assessment & Final Evaluation Flow
- **Assessments**: Staff supervisors conduct formal evaluations (e.g., 'Mid-Term', 'Final'), recording numerical `Marks` and descriptive `Remarks` in the `assessment` table against the `AttachmentID`.
- **Final Report Submission**: At the end of the placement, students upload a final report document into the `finalreport` table. 
- **Clearance**: Once the final report is 'Approved' and all required assessments and logbook entries are met, the attachment achieves a 'Cleared' status, triggering account deactivation for the student.

## 6. Identified Business Logic Flaws
During system implementation and review, several logic gaps or flaws were identified:
1. **Host Organization Verification**: Host Organizations can be created during student applications without a formal Admin vetting/approval step, meaning students could theoretically place themselves at illegitimate organizations.
2. **Indefinite Active Status**: Students' system access is only revoked (set to 'Inactive') when they submit a final report. If a student simply abandons the placement or forgets to submit, their account remains indefinitely active.
3. **Assessment Code Communication**: The Host Org generates an Assessment Code for the Lecturer, but there is no built-in messaging module to securely transmit this code, relying on external emails or calls which could be delayed.
4. **Concurrent Placements Check**: The database schema does not strongly prevent a student from initiating multiple overlapping "Ongoing" attachments across different Host Orgs simultaneously.
5. **Supervisor Load Balancing**: When assigning Lecturers to supervise students (`supervision` table), the system lacks an automated check to limit how many students one Lecturer can be assigned. Furthermore, for their first and final assessments, each student should only have a maximum of two lecturers assigned to them, but this limit is not currently enforced by the database schema or application logic.
