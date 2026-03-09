# Goal Description
The objective is to allow Host Organizations to have multiple supervisors (mentors) without creating duplicate organization accounts. This separates the "Organization" from the "Individual Supervisor." Organizations will manage their own mentors and assign them to placed students. Mentors will have their own logins to review student logbooks and conduct assessments.

## User Review Required
> [!IMPORTANT]
> The introduction of a new `HostMentor` role means we need to update the authentication system and create a distinct dashboard for mentors, separate from the primary Host Organization dashboard.

## Proposed Changes

### Database Schema
*   **[NEW] `hostmentor` table**: Stores mentor details. Columns: `MentorID` (PK), `HostOrgID` (FK), `UserID` (FK), `FullName`, `Email`, `PhoneNumber`.
*   **[MODIFY] `attachment` table**: Add `MentorID` (FK to `hostmentor.MentorID`) to link a student's attachment to a specific mentor within the organization.
*   **[MODIFY] `users` table**: Accommodate a new role `Role = 'HostMentor'`.

---
### Models

#### [NEW] `app/Models/HostMentor.php`
- Model to handle CRUD operations for mentors (create, read, update, delete by the Host Organization).
- Method to fetch students assigned to a specific mentor.

#### [MODIFY] `app/Models/Attachment.php`
- Update schema definition and SQL queries to include `MentorID`.
- Update `assignMentor($attachmentId, $mentorId)` logic.

#### [MODIFY] `app/Models/User.php`
- Ensure `HostMentor` role is supported during role checks and authentication.

---
### Controllers

#### [MODIFY] `app/Controllers/HostController.php`
- Add `manageMentors()`: Fetch all mentors for the logged-in Host Org.
- Add `addMentor()`: Create a new user account with role `HostMentor`, insert into `hostmentor` table, and send an email with initial credentials.
- Add `assignStudentToMentor()`: Handle the assignment of an attached student to a specific mentor.

#### [NEW] `app/Controllers/MentorController.php`
- Handles the dashboard and actions specifically for the logging-in Mentor.
- Methods: `dashboard()`, `viewStudents()`, `reviewLogbook()`, `submitAssessment()`.

#### [MODIFY] `app/Controllers/AuthController.php`
- Update redirect logic inside `login()`: If `Role == 'HostMentor'`, redirect to `/mentor/dashboard`.

---
### Views

#### [NEW] `app/Views/host/mentors.php`
- A management page for Host Orgs to list, add, and remove their mentors.

#### [NEW] `app/Views/host/assign_mentor.php`
- A modal or page where the Host Org can select a student and assign them from a dropdown of available mentors.

#### [NEW] `app/Views/mentor/dashboard.php`
- The landing page for the `HostMentor` role showing their specific assigned students.

#### [MODIFY] `app/Views/host/dashboard.php`
- Add links to the "Manage Mentors" section in the sidebar.

#### [MODIFY] `app/Views/host/students.php`
- Show which mentor is assigned to each student, alongside an "Assign/Change Mentor" button.

---
### Routing

#### [MODIFY] `public/index.php`
- Add new routes for the Host Org to manage mentors:
  - `GET /host/mentors`
  - `POST /host/mentors/add`
  - `POST /host/assign-mentor`
- Add new routes for the `Mentor` role:
  - `GET /mentor/dashboard`
  - `GET /mentor/students`
  - `GET /mentor/logbooks`

## Verification Plan

### Manual Verification
1.  **Host Org Workflow**: Log in as a Host Org, navigate to "Manage Mentors", and successfully create a new mentor. Verify an email with credentials would be dispatched.
2.  **Assignment Workflow**: In the Host Org dashboard, locate an attached student and assign them to the newly created mentor. Verify the database updates the `Attachment` table correctly.
3.  **Mentor Login**: Log in using the newly created mentor credentials. Verify the redirect lands on the Mentor Dashboard.
4.  **Mentor Supervision Validation**: Verify that the mentor can only see the students explicitly assigned to them, and not all students at the organization.
