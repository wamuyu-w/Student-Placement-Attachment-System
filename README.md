# Student Placement Attachment System

## Overview
This system is a web-based application designed to manage student placements and attachments. It facilitates the interaction between Students, Staff (Lecturers/Supervisors), Host Organizations, and Administrators.

## Folder Structure

The project is organized into modular directories based on functionality or user role.

### **Root Directory**
- `index.php`: The landing page of the application.
- `config.php`: Database connection configuration and helper functions.
- `index.css`: Styles for the landing page.
- `attachmentmanagementsystem.sql`: Database schema import file.

### **Applications/**
Contains files related to attachment applications.
- `student-applications.php`: Form for students to apply for attachments.
- `admin-applications.php`: Admin view to manage applications.
- `host-org-applications.php`: Host organization view of applications.
- `process-*.php`: Scripts handling application processing logic.

### **Assessment/**
Handles student assessment modules.
- `staff-assessment.php`: Interface for staff to assess students.
- `process-assessment.php`: Backend logic for saving assessments.

### **Dashboards/**
Contains the main dashboard interfaces for each user role.
- **Admin/**: Contains the main `admin-dashboard.php` and its assets. Note that specific admin sub-pages (Students, Supervisors, etc.) are located in their respective functional directories (e.g., `Students/`, `Supervisor/`) but are linked from here.
- `student-dashboard.php`, `staff-dashboard.php`, `host-org-dashboard.php`: Main dashboards for other roles.
- Corresponding `.css` and `.js` files for each dashboard.

### **Logbook/**
Manages student logbook entries.
- `student-logbook.php`: Interface for students to enter logbook activities.
- `staff-logbook.php`: Interface for staff to review logbooks.
- `process-*.php`: Scripts for processing entries and comments.

### **Login Pages/**
Authentication pages for all users.
- `login-student.php`, `login-staff.php`, `login-host-org.php`: Individual login pages.
- `logout.php`: Session destruction script.
- `login.css`, `login.js`: Shared styles and scripts for login pages.

### **Opportunities/**
Management of attachment opportunities.
- `admin-opportunities-management.php`: Admin interface to create and manage opportunities.
- `host-management-opportunities.php`: Host organization interface to post opportunities.
- `student-opportunities.php`: Student view of available opportunities.
- `process-*.php`: Backend logic for adding/applying to opportunities.

### **Reports/**
System reporting functionality.
- `admin-reports.php`: Admin interface for generating system reports.
- `host-org-reports.php`, `staff-reports.php`, `student-reports.php`: Report views for other roles.

### **Settings/**
User account settings.
- `*-settings.php`: Settings pages for Admin (in root or respective folder), Staff, Student, etc.
- `process-update-password.php`: Password update logic.

### **Sign-up Pages/**
Registration pages.
- `signup-student.php`: Student registration form.
- `signup-host-org.php`: Host organization registration form.
- `process-signup-*.php`: Registration processing scripts.

### **Students/**
Student management for admins.
- `admin-students.php`: **Active** admin page for managing the list of students.
- `staff-students.php`, `host-org-students.php`: Views for staff and host orgs to see their students.
- `process-clear-student.php`: Logic for clearing a student's attachment.

### **Supervisor/**
Supervisor management.
- `admin-supervisors.php`: Admin page for managing supervisors and assignments.
- `student-supervisor.php`: Student view of their assigned supervisor.
- `process-assign-supervisor.php`: Logic for assigning supervisors.

### **assets/**
Global assets.
- `css/`: Contains `global.css`, `theme.css`.
- `js/`: Global JavaScript files.
- `images/`: Static images (Logos, etc.).

## Setup Instructions
1. Import `attachmentmanagementsystem.sql` into your MySQL database.
2. Configure `config.php` with your database credentials.
3. Serve the application using a PHP-enabled server (e.g., XAMPP, Apache).
