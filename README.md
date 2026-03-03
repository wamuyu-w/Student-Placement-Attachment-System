# Student Placement Attachment System - File Hierarchy & Documentation

This document provides a comprehensive breakdown of every file within the system, organized by their respective directories. It serves as a guide to understanding the purpose of each script, view, and asset in the application.

## Root Directory (`/`)
The foundation of the application, including central configuration and entry points.

- `attachmentmanagementsystem.sql`: The SQL script used to initialize the database schema and default data.
- `config.php`: Central database connection configuration file. It may also contain core helper functions and session starts.
- `index.php`: The main landing page / entry point for the application.
- `index.css`: Stylesheet specifically for the landing page and general root elements.
- `README.md`: The standard high-level project overview documentation.
- `SCI400-Changes`: A text file or log tracking specific requirements or adjustments made for the SCI400 course project.
- `.gitignore`: Specifies intentionally untracked files to ignore for Git version control.

---

## `api/`
Handles asynchronous data fetching for the frontend.
- `fetch-dashboard-activity.php`: An endpoint that returns recent activity data (JSON format) to dynamically update dashboards without refreshing.

---

## `Applications/`
Contains logic and interfaces for the attachment/placement application process.
- `admin-applications.php`: Admin interface for viewing, reviewing, and managing all student applications.
- `host-org-applications.php`: Dashboard for Host Organizations to see applications submitted to them by students.
- `process-application-status.php`: Backend logic to transition an application's status (e.g., Pending -> Approved).
- `process-apply-session.php`: Handles session/temporary state variables during multi-step application flows.
- `process-program-application.php`: Backend script that persists a student's attachment program application to the database.
- `process-register-attachment.php`: Logic that finalizes a student's placement once they have found/accepted an attachment.
- `process-update-application-status.php`: Updates actions tied to specific status changes.
- `student-applications.php`: Interface for students to track the status of their submitted applications.

---

## `Assessment/`
Modules for evaluating students during or at the end of their placement.
- `process-assessment.php`: Backend logic that calculates and saves assessment scores to the database.
- `staff-actual-assessment.php`: The specific form where a staff member inputs scores and remarks for a student.
- `staff-assessment.php`: A listing/dashboard interface for staff to see all students they need to assess.
- `staff-enter-assessment-code.php`: Security measure requiring staff to input a specific code before unlocking an assessment form.
- `view-assessment.php`: Interface for authorized users to review a completed past assessment.

---

## `assets/`
Static assets required for styling, branding, and frontend logic.
- `css/global.css`: Global baseline styles applied across most/all pages.
- `css/theme.css`: Theme-specific configurations (colors, typography, common layouts).
- `CUEA_Ext-01.jpg`: A branding background or hero image for the university.
- `cuea-logo.png`: The Catholic University of Eastern Africa (CUEA) official logo.
- `js/dashboard-updates.js`: JavaScript responsible for polling or handling real-time data updates on dashboards.

---

## `Dashboards/`
The primary post-login hub interfaces for the four main user roles.
- `dashboard.css`: Shared styles utilized across all role dashboards.
- **Admin/**
  - `admin-dashboard.php`: The primary control panel view for administrators.
  - `admin-dashboard.css`: Specific styling overlays for the Admin dashboard.
  - `admin-dashboard.js`: Interactive elements and data initialization for the Admin view.
  - `admin-reports.php`: Admin interface for generating system-wide reports (Alias/link to reports module).
  - `admin-students.php`: Admin interface to view and manage students.
  - `admin-supervisors.php`: Admin interface to view and manage supervisors.
- **Host Organization**
  - `host-org-dashboard.php`: Post-login hub for external company representatives.
  - `host-org-dashboard.css` & `host-org-dashboard.js`: Styling and interactive logic for Host Orgs.
- **Staff (Supervisor/Lecturer)**
  - `staff-dashboard.php`: Post-login hub for academic supervisors.
  - `staff-dashboard.css` & `staff-dashboard.js`: Styling and interactive logic for Staff.
- **Student**
  - `student-dashboard.php`: Post-login hub for students on attachment.
  - `student-dashboard.css` & `student-dashboard.js`: Styling and interactive logic for Students.

---

## `documentation/`
Detailed technical and business requirement documentation.
- `business_logic.md`: Rules and workflows dictating the application's behavior.
- `review/LECTURER_EVALUATION.md`: Documentation detailing the process of lecturer evaluations.
- `review/PROCESS_GAP_ANALYSIS.md`: System analysis detailing missing features or workflows.
- `review/SETUP.md`: Comprehensive guide to deploying and setting up the system locally.
- `review/README.md`: Index for the review documentation folder.
- `review/LICENSE`: Intellectual property and usage license terms.

---

## `Logbook/`
Handles the daily/weekly activity records of students on attachment.
- `host-org-logbook.php`: Interface for Host Organization supervisors to review their students' logbooks.
- `process-logbook-comment.php`: Backend logic allowing supervisors to add feedback onto a student's entry.
- `process-logbook-entry.php`: Backend logic for students submitting a new daily/weekly activity log.
- `staff-logbook.php`: Interface for Academic Staff to review their assigned students' logbooks.
- `student-logbook.php`: Interface for Students to write new entries and view chronological past entries.

---

## `Login Pages/`
Authentication views and backend processing scripts.
- `login.css` & `login.js`: Shared styling and frontend form validation for all login pages.
- `login-host-org.php`: Form used by Host Organization accounts to sign in.
- `login-staff.php`: Form used by Lecturers/Staff to sign in.
- `login-student.php`: Form used by Students to sign in.
- `logout.php`: Safely destroys the user session and redirects to the landing page.
- *(Note: `host-organization-login.php`, `staff-login.php`, `student-login.php` are likely aliases or legacy versions of the main login files).*

---

## `Opportunities/`
System for Host Organizations to post vacancies and for Students to apply to them.
- `admin-opportunities-management.php`: Admin interface used to moderate or manage all opportunities system-wide.
- `edit-opportunity-modal.php`: A UI partial (modal) included on pages to allow quick inline editing of an opportunity.
- `host-management-opportunities.php`: Dashboard for Host Organizations to create, edit, and delete their own vacancy posts.
- `process-add-opportunity.php`: Backend logic to insert a new opportunity into the database.
- `process-apply-opportunity.php`: Backend logic mapping a student's application to a specific opportunity.
- `process-delete-opportunity.php`: Backend logic to securely remove an opportunity.
- `process-edit-opportunity.php`: Backend logic updating details of an existing opportunity.
- `student-opportunities.php`: Interface for active students to discover and read about available attachments.
- `opportunities-flow.html` & `opportunities-flow-admin.html`: Frontend HTML mockups showing the intended UI/UX flow.
- `opportunities-flow.css`: Styles specific to the opportunities display grids and cards.

---

## `Reports/`
Generates both screen views and print-ready formats (PDFs, Tables) of system data.
- `admin-reports.php`: Master reporting suite for Admins to pull metrics on placements.
- `host-org-reports.php`: Reporting view for Host Orgs summarizing their intake.
- `staff-reports.php`: Reporting view for Staff showing metrics on their assigned supervisees.
- `student-reports.php`: Reporting view for Students regarding their completion status and history.
- `process-upload-report.php`: Backend logic handling file uploads (e.g., student uploading their Final Attachment Report document).
- **Printable Views**
  - `print-completion.php`: Generates a printable Completion Certificate or Summary.
  - `print-grades.php`: Generates a printable page of finalized student assessment scores.
  - `print-logbook.php`: Generates a continuous, printable flow of a student's entire semester logbook.
  - `print-supervisors.php`: Generates a printable list of supervisor allocations.

---

## `Settings/`
User profile management and system preferences.
- `admin-settings.php`, `staff-settings.php`, `student-settings.php`: Profile adjustment interfaces for their respective roles.
- `first-login-update.php`: Forced interstitial page seen when an account logs in for the very first time.
- `process-first-login-update.php`: Captures the POST request from the first login screen to update initial profile metrics.
- `process-update-password.php`: Secure backend logic for changing user passwords, implementing hashing verification.

---

## `Sign-up Pages/`
Public facing registration forms for creating new accounts.
- `signup.css`: Consistent styling applied to the registration pages.
- `signup-host-org.php`: Form for a company to request a Host Organization account.
- `signup-hostorg.js`: Frontend validation ensuring Host Org entries follow constraints.
- `process-signup-host-org.php`: Backend logic saving the Host Org request to the database.
- `signup-student.php`: Form for a Student to create their system account.
- `signup-student.js`: Frontend validation ensuring Student inputs match required masking (e.g., valid registration numbers).
- `process-signup-student.php`: Backend logic securely creating the student profile.

---

## `Students/`
Data management interfaces for student profiles and tracking lists.
- `admin-students.php`: Massive grid for Admins to oversee all enrolled students.
- `host-org-students.php`: Screen for Host Organizations to see strictly the students attached to them.
- `staff-students.php`: Screen for Academic Supervisors to interact with their assigned pool of students.
- `process-add-student.php`: Backend logic to manually insert an individual student record.
- `process-bulk-students.php`: Backend logic handling the upload/parsing of a CSV to import multiple students simultaneously.
- `process-clear-student.php`: Backend logic that handles the clearing and graduation workflow for a student finishing attachment.
- `view-student-progress.php`: Detailed drill-down view showing a single student's timeline, from application to final assessment.

---

## `Supervisor/`
Management modules mapping staff and host company supervisors to students.
- `admin-supervisors.php`: Admin interface defining who the supervisors are.
- `host-org-supervision.php`: Interface where Host Organizations assign specific company representatives to specific students.
- `staff-supervision.php`: Interface for Lecturers to track their overall supervising duties.
- `student-supervisor.php`: Interface for Students to see the contact details of their assigned academic and field supervisors.
- `process-add-supervisor.php`: Logic to insert a single new supervisor.
- `process-assign-supervisor.php`: The backend matchmaking logic locking a student to a supervisor entity.
- `process-bulk-supervisors.php`: Logic to mass-upload supervisors via CSV file.
- `process-generate-code.php`: Handles the creation (and possible emailing/storage) of the secure Assessment unlock codes for staff.
- `process-schedule-assessment.php`: Backend logic allowing dates and times to be set for an upcoming field assessment visit.

1. Import `attachmentmanagementsystem.sql` into your MySQL database.
2. Configure `config.php` with your database credentials.
3. Serve the application using a PHP-enabled server (e.g., XAMPP, Apache).
