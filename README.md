# Module Testing Documentation

## 6.5 Module Testing
The module testing phase was conducted to verify the functionality of individual modules of the Student Placement Attachment System and ensure that each module performs as specified. Each functional module was tested independently using test data, and the actual output was compared with the expected results. The following modules were tested:

*   Student Registration Module
*   Opportunity Management Module
*   Application & Attachment Module
*   Logbook Management Module
*   Login & Authentication Module

A summary of the testing results for the **Login & Authentication Module** is shown in the table below as an example.

| Test Case | Input Data | Expected Output | Actual Output | Pass/Fail |
| :--- | :--- | :--- | :--- | :--- |
| Student Login - Valid Credentials | Email: student@test.com, Pass: valid123 | Redirect to Student Dashboard | Redirected to Student Dashboard | Pass |
| Staff Login - Valid Credentials | Email: staff@test.com, Pass: staff123 | Redirect to Staff Dashboard | Redirected to Staff Dashboard | Pass |
| Host Organization Login - Valid Credentials | Email: host@test.com, Pass: host123 | Redirect to Host Org Dashboard | Redirected to Host Org Dashboard | Pass |
| Login with Invalid Password | Email: student@test.com, Pass: wrongpass | Error: "Invalid email or password" | Error displayed: "Invalid email or password" | Pass |
| Login with Unregistered Email | Email: unknown@test.com, Pass: 123456 | Error: "Invalid email or password" | Error displayed: "Invalid email or password" | Pass |
| Login with Empty Fields | Email: [Empty], Pass: [Empty] | Validation Error: "Fields cannot be empty" | Validation Error displayed | Pass |

Similar tests were performed for all other modules, and the outcomes confirmed that the system meets its functional requirements as specified in the design. User Acceptance Testing (UAT) was also performed with sample users to validate usability and alignment with user needs.
