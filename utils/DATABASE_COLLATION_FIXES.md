# Database Collation and Schema Fixes

## Database Collation Analysis

Based on `attachmentmanagementsystem.sql`:

### Collation Settings
- **Database/Table Collation**: `utf8mb4_general_ci`
- **Character Set**: `utf8mb4`
- **Case Sensitivity**: Case-insensitive (the `_ci` suffix means "case insensitive")

### Impact on Login
The `utf8mb4_general_ci` collation is **case-insensitive**, which means:
- ✅ Username comparisons work regardless of case (e.g., "Admin" = "admin")
- ✅ This is generally good for usernames
- ⚠️ However, exact case matching in SQL queries is still recommended for consistency

## Issues Found and Fixed

### 1. Role Name Mismatch - Host Organization
**Problem:**
- SQL Schema: `'Host Organization'` (with space)
- PHP Code: `'HostOrganization'` (no space)
- **Result**: Host organization logins would fail

**Fix Applied:**
- Updated `login-host-org.php` to use `'Host Organization'` (with space)

### 2. Non-existent Role - Supervisor
**Problem:**
- PHP Code checked for: `'Supervisor'` role
- SQL Schema only has: `'Lecturer'` and `'Admin'`
- **Result**: Any user with 'Supervisor' role would fail to login

**Fix Applied:**
- Removed `'Supervisor'` from the role check in `login-staff.php`
- Now only checks for `'Lecturer'` and `'Admin'`

### 3. Table Name Casing Mismatch
**Problem:**
- SQL Schema uses: lowercase table names (`users`, `student`, `lecturer`, `hostorganization`)
- PHP Code used: PascalCase (`Users`, `Student`, `Lecturer`, `HostOrganization`)
- **Result**: On Linux systems (case-sensitive), queries would fail

**Fix Applied:**
- Updated all login files to use lowercase table names matching the SQL schema:
  - `Users` → `users`
  - `Student` → `student`
  - `Lecturer` → `lecturer`
  - `HostOrganization` → `hostorganization`

### 4. Password Storage Format
**Found:**
- Passwords in SQL dump are stored as **plain text** (e.g., `'password123'`)
- PHP code expects **hashed passwords** (bcrypt format)

**Fix Applied:**
- Added backward compatibility in all login files
- Now supports both:
  - Hashed passwords (bcrypt) - preferred
  - Plain text passwords - auto-converts to hash on successful login

## Database Schema Summary

### Users Table Structure
```sql
CREATE TABLE `users` (
  `UserID` int(11) NOT NULL,
  `Username` varchar(50) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Role` varchar(30) NOT NULL,
  `Status` varchar(20) DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

### Valid Roles in Database
- `'Student'` ✅
- `'Lecturer'` ✅
- `'Admin'` ✅
- `'Host Organization'` ✅ (with space)

### Sample Data from SQL
```sql
INSERT INTO `users` VALUES
(1, '1049088', 'password123', 'Student', 'Active'),
(5, 'L001', 'password123', 'Lecturer', 'Active'),
(8, 'L190', 'password123', 'Admin', 'Active'),
(9, 'H001', '', 'Host Organization', 'Active');
```

## Recommendations

1. **Hash All Passwords**: Run the password hashing utility to convert all plain text passwords to bcrypt hashes
2. **Case Consistency**: Always use exact case when comparing roles in PHP code
3. **Table Names**: Use lowercase table names to match the schema (already fixed)
4. **Testing**: Test login with:
   - Student: `1049088` / `password123`
   - Lecturer: `L001` / `password123`
   - Admin: `L190` / `password123`
   - Host Org: `H001` / (empty password - may need to be set)

## Files Modified

1. `Login Pages/login-student.php` - Fixed table names, role check
2. `Login Pages/login-staff.php` - Fixed table names, removed 'Supervisor' role
3. `Login Pages/login-host-org.php` - Fixed table names, fixed role name to 'Host Organization'
