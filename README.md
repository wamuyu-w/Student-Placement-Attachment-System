# Student Placement Attachment System

## Overview
The **Student Placement Attachment System** is a comprehensive web-based application designed to streamline and manage the industrial attachment and placement process for university students. It provides a centralized platform for the four main stakeholders: Students, Lecturers (Supervisors), Host Organizations and System Administrators, bridging the gap between academic institutions and the industry.

## Features

### 🎓 Student Portal
* **Profile Management**: Maintain academic details, contact information and eligibility status.
* **Opportunities Board**: Browse and apply for available attachment and internship opportunities posted by host organizations.
* **Logbook Tracking**: Submit daily and weekly logbook activities for review by both host and academic supervisors.
* **Final Report**: Upload and submit the final industrial attachment report for grading.

### 🏢 Host Organization Portal
* **Automated Onboarding**: Seamless account creation and profile management for company representatives.
* **Opportunity Management**: Post, edit and manage available attachment vacancies.
* **Applicant Tracking**: Review student applications, accept/reject candidates and review resumes.
* **Supervision**: Review assigned students' logbook entries and provide industry-side comments and feedback.

### 👨‍🏫 Lecturer / Supervisor Module
* **Student Supervision**: View lists of assigned students for academic supervision.
* **Logbook Review**: Monitor and provide academic feedback on students' logbook entries.
* **Assessment & Grading**: Conduct structured assessments, assign marks based on specific criteria and provide overall remarks.

### ⚙️ Administrator Dashboard
* **System Overview**: Manage users across all roles (Students, Staff, Host Orgs).
* **Application Management**: Oversee the entire placement process and handle bulk uploads (e.g., student lists).
* **Settings & Configuration**: Configure system-wide parameters and ensure smooth operation.

## Technology Stack
* **Backend**: PHP 8+ using a Custom MVC (Model-View-Controller) Framework.
* **Frontend**: HTML5, CSS3 and JavaScript.
* **Database**: MySQL .

## Directory Structure
```text
Student-Placement-Attachment-System/
├── app/                  # Application core
│   ├── Controllers/      # Application logic and request handling
│   ├── Models/           # Database interactions and business objects
│   ├── Views/            # UI templates and pages
│   └── Core/             # Core MVC routing and base classes
├── assets/               # Static assets (CSS, JS, Images, Uploads)
├── documentation/        # Project documentation 
├── public/               # Document root
│   └── index.php         # Application entry point/front controller
├── attachmentmanagementsystem.sql # Database schema and initial data dump
├── .env                  # Environment configuration variables
└── README.md             # Project documentation (this file)
```

## Installation and Setup

### Prerequisites
* A web server such as Apache or Nginx (e.g., via XAMPP, WAMP, or LAMP stack).
* PHP 8.0 or higher.
* MySQL or MariaDB database server.

### Steps
1. **Clone or Extract the Project:**
   Place the project directory (`Student-Placement-Attachment-System`) into your web server's root directory (e.g., `htdocs` for XAMPP or `/var/www/html` for standard Linux setups).

2. **Database Configuration:**
   * Open your database manager (like phpMyAdmin or MySQL CLI).
   * Create a new database named `attachmentmanagementsystem`.
   * Import the provided `attachmentmanagementsystem.sql` file into the newly created database to set up the tables and structure.

3. **Environment Setup:**
   * Ensure a `.env` file exists in the root directory. If not, create one.
   * Add necessary environment variables (e.g., `APP_KEY`, `USER_MAIL` for email services).
   * *Note: Ensure your database connection settings in `app/Core/Model.php` or `app/Core/Database.php` (if applicable) match your local database credentials.*

4. **Web Server Configuration:**
   * The application is routed through `public/index.php`. 
   * Ensure your web server allows `.htaccess` overrides if using Apache, so all requests are properly funneled to the `public/` directory router.

5. **Access the Application:**
   * Open your web browser and navigate to the project URL: 
     `http://localhost/Student-Placement-Attachment-System/public/`

