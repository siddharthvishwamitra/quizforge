# QuizForge - Exam Management System

A PHP & MySQL-based form management system for handling quiz participation registrations, admit cards, and results. Designed for both participants and admins to streamline registration and management.

---

## Features

### For Participants:
- Submit registration form
- Print submitted registration form
- View and Print admit card
- View student results

### For Admins:
- Manage system settings (enable/disable public pages)
- View all student registrations
- Sort and filter students (e.g., by roll number, name)
- Edit, update, or delete student records
- Assign roll numbers in bulk
- Manage results (enter/update results)
- Sort results by roll number or marks
- Print clean student list for invigilators

---

## Installation

### 1. Clone the Repository
```bash
git clone https://github.com/siddharthvishwamitra/quizforge.git
```

## MYSQL Setup

1. **Locate the SQL File:**
   - You will find a file named `phpquiz.sql` in the root folder of the repository.

2. **Create a Database:**
   - Open **phpMyAdmin** (or any MySQL client).
   - Create a new database:
     ```sql
     CREATE DATABASE quizmg;
     ```

3. **Import the SQL File:**
   - Select the newly created database.
   - Go to the **Import** tab in phpMyAdmin.
   - Choose the `phpquiz.sql` file from the repository.
   - Click **Go** to import the file. This will create all necessary tables and data for the application.

4. **Configure Database Connection:**
   - Edit the `staff/config.php` file to update the database credentials:
     ```php
     $host = "localhost";
     $user = "your_db_user";        // Your database username
     $pass = "your_db_password";    // Your database password
     $db   = "quizmg";              // The database you just created
     ```
   - Save the file.

Now your database should be set up and ready to use!
