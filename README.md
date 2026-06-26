# Student Placement & Attachment System

## Overview
The Student Placement & Attachment System is a comprehensive web-based application designed to streamline the management of student industrial attachments, internships, and placements. It provides a unified, centralized platform facilitating interaction between students, host organizations, academic staff, and system administrators.

## Core Features
- **Student Module:** Browse and apply for attachment opportunities, and maintain digital daily logbooks.
- **Host Organization Module:** Post attachment vacancies, review student applications, and submit performance assessments.
- **Supervisor & Staff Module:** Monitor assigned students, review and approve logbooks, and conduct academic assessments.
- **Administrator Module:** Manage system users (students, staff, hosts) and generate comprehensive performance and placement reports.

## Technology Stack
- **Backend Architecture:** Custom MVC framework in PHP
- **Database:** MySQL
- **Dependencies:** PHPMailer (managed via Composer)
- **Frontend:** Standard web technologies (HTML, CSS, JavaScript)

## Setup and Installation
1. Clone the repository into your local web server environment (e.g., `xampp/htdocs`).
2. Import the database schema provided in the `/SQL` directory into your MySQL instance.
3. Configure environment variables by copying `.env.example` to `.env` and updating the database credentials.
4. Execute `composer install` to install required dependencies.
5. Serve the application by pointing your local server to the `public/` directory.
