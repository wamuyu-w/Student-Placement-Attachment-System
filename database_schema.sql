CREATE Database AttachmentManagementSystem;
Use AttachmentManagementSystem;
CREATE TABLE Users (
    UserID INT PRIMARY KEY AUTO_INCREMENT,
    Username VARCHAR(50) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL,
    Role VARCHAR(30) NOT NULL,
    Status VARCHAR(20) DEFAULT 'Active' 
);

CREATE TABLE Student (
    StudentID INT PRIMARY KEY,
    UserID INT UNIQUE,
    FirstName VARCHAR(50),
    LastName VARCHAR(50),
    Course VARCHAR(100),
    Faculty VARCHAR(100),
    YearOfStudy INT,
    PhoneNumber VARCHAR(20),
    Email VARCHAR(100),
    EligibilityStatus VARCHAR(20),
    FOREIGN KEY (UserID) REFERENCES Users(UserID)
);

CREATE TABLE HostOrganization (
    HostOrgID INT PRIMARY KEY AUTO_INCREMENT,
    OrganizationName VARCHAR(150) NOT NULL,
    ContactPerson VARCHAR(100),
    Email VARCHAR(100),
    PhoneNumber VARCHAR(20),
    PhysicalAddress VARCHAR(200)
);

CREATE TABLE Lecturer (
    LecturerID INT PRIMARY KEY AUTO_INCREMENT,
    UserID INT UNIQUE,
    StaffNumber VARCHAR(30) UNIQUE,
    Name VARCHAR(100),
    Department VARCHAR(100),
    Faculty VARCHAR(100),
    Role VARCHAR (100), -- CAN BE AN ADMIN (WHICH IS AN INDUSTRIAL ATTACHMENT COORDINATOR OR A SUPERVISOR
    FOREIGN KEY (UserID) REFERENCES Users(UserID)
);

CREATE TABLE AttachmentOpportunity (
    OpportunityID INT PRIMARY KEY AUTO_INCREMENT,
    HostOrgID INT,
    Description TEXT,
    EligibilityCriteria TEXT,
    ApplicationStartDate DATE,
    ApplicationEndDate DATE,
    Status VARCHAR(20),
    FOREIGN KEY (HostOrgID) REFERENCES HostOrganization(HostOrgID)
);

CREATE TABLE AttachmentApplication (
    ApplicationID INT PRIMARY KEY AUTO_INCREMENT,
    StudentID INT,
    ApplicationDate DATE,
    ApplicationStatus VARCHAR(20),
    RejectionReason TEXT,
    FOREIGN KEY (StudentID) REFERENCES Student(StudentID)
);

CREATE TABLE Attachment (
    AttachmentID INT PRIMARY KEY AUTO_INCREMENT,
    StudentID INT UNIQUE,
    HostOrgID INT,
    StartDate DATE,
    EndDate DATE,
    ClearanceStatus VARCHAR(20),
    AttachmentStatus VARCHAR(20),
    FOREIGN KEY (StudentID) REFERENCES Student(StudentID),
    FOREIGN KEY (HostOrgID) REFERENCES HostOrganization(HostOrgID)
);

CREATE TABLE Logbook (
    LogbookID INT PRIMARY KEY AUTO_INCREMENT,
    AttachmentID INT UNIQUE,
    IssueDate DATE,
    Status VARCHAR(20), -- Ongoing or Complete
    FOREIGN KEY (AttachmentID) REFERENCES Attachment(AttachmentID)
);

CREATE TABLE LogbookEntry (
    EntryID INT PRIMARY KEY AUTO_INCREMENT,
    LogbookID INT,
    EntryDate DATE,
    Activities TEXT,
    HostSupervisorComments TEXT, -- BY THE HOST ORGANIZATION - feedback on tasks done
    FOREIGN KEY (LogbookID) REFERENCES Logbook(LogbookID)
);

CREATE TABLE Supervision (
    SupervisionID INT PRIMARY KEY AUTO_INCREMENT,
    LecturerID INT,
    AttachmentID INT,
    FOREIGN KEY (LecturerID) REFERENCES Lecturer(LecturerID),
    FOREIGN KEY (AttachmentID) REFERENCES Attachment(AttachmentID)
);
-- Supervision type not needed since it's not known by the student

CREATE TABLE Assessment (
    AssessmentID INT PRIMARY KEY AUTO_INCREMENT,
    AttachmentID INT,
    AssessmentDate DATE,
    AssessmentType VARCHAR(20), -- Can be the first or final supervision
    Marks DECIMAL(5,2),
    Remarks TEXT,
    FOREIGN KEY (AttachmentID) REFERENCES Attachment(AttachmentID)
);

CREATE TABLE FinalReport (
    ReportID INT PRIMARY KEY AUTO_INCREMENT,
    AttachmentID INT UNIQUE,
    SubmissionDate DATE,
    ReportFile VARCHAR(255),
    Status VARCHAR(20),
    FOREIGN KEY (AttachmentID) REFERENCES Attachment(AttachmentID)
);



-- Insert sample data for testing
-- Default password for all test accounts: password123 (hashed with password_hash)
INSERT INTO students (username, password, student_id, first_name, last_name, email, course, year_of_study) VALUES
('student1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1049727', 'William', 'Kamau', 'j@cuea.ac.ke', 'Computer Science', 3),
('student2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1036984', 'Jane', 'Ndungu', 'jane.smith@cuea.ac.ke', 'Business Administration', 2);

INSERT INTO staff (username, password, staff_id, first_name, last_name, email, department, role) VALUES
('staff1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3901', 'Dr. Mary', 'Kinuthia', 'mary.kinuthia@cuea.ac.ke', 'Computer Science', 'Lecturer'),
('staff2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3902', 'Prof. James', 'Wachira', 'james.wachira@cuea.ac.ke', 'Industrial Attachment Coordinator', 'Coordinator');

INSERT INTO host_organizations (username, password, organization_name, email, contact_person, phone) VALUES
('org1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Tech Solutions Ltd', 'info@techsolutions.co.ke', 'Peter Kamau', '+254712345678'),
('org2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Business Partners Inc', 'contact@businesspartners.co.ke', 'Sarah Wanjiku', '+254723456789');

-- Note: The password hash above is for 'password123'
-- To create new password hashes, use: password_hash('your_password', PASSWORD_DEFAULT)
