# 🗄️ Database Migration: SQLite (Local) → MySQL (Hostinger)

## Overview

Migrate your local SQLite database to MySQL on Hostinger production server.

---

## Method 1: Using Laravel Tinker (Recommended)

### Step 1: Export Data from Local SQLite

On your **local Mac**, run:

```bash
cd /Users/rajesh/Documents/GitHub/v2student

# Start Tinker
php artisan tinker
```

Then export data (run these commands in Tinker):

```php
// Export Students
$students = \App\Models\Student::all();
file_put_contents('storage/app/students_export.json', json_encode($students->toArray()));

// Export Courses
$courses = \App\Models\Course::all();
file_put_contents('storage/app/courses_export.json', json_encode($courses->toArray()));

// Export Batches
$batches = \App\Models\Batch::all();
file_put_contents('storage/app/batches_export.json', json_encode($batches->toArray()));

// Export Enrollments
$enrollments = \App\Models\Enrollment::all();
file_put_contents('storage/app/enrollments_export.json', json_encode($enrollments->toArray()));

// Export Payments
$payments = \App\Models\Payment::all();
file_put_contents('storage/app/payments_export.json', json_encode($payments->toArray()));

// Export Users
$users = \App\Models\User::all();
file_put_contents('storage/app/users_export.json', json_encode($users->toArray()));

// Export Assessments
$assessments = \App\Models\Assessment::all();
file_put_contents('storage/app/assessments_export.json', json_encode($assessments->toArray()));

// Export Assessment Results
$results = \App\Models\AssessmentResult::all();
file_put_contents('storage/app/assessment_results_export.json', json_encode($results->toArray()));

// Export Certificates
$certificates = \App\Models\Certificate::all();
file_put_contents('storage/app/certificates_export.json', json_encode($certificates->toArray()));

exit
```

### Step 2: Upload JSON Files to Hostinger

Upload the exported JSON files to Hostinger:
- Via File Manager: Upload to `storage/app/` folder
- Or via SCP:
  ```bash
  scp -P 65002 storage/app/*_export.json u820431346@145.14.146.15:~/public_html/v2student/storage/app/
  ```

### Step 3: Import Data on Hostinger

Connect to Hostinger and import:

```bash
ssh -p 65002 u820431346@145.14.146.15
cd ~/public_html/v2student

php artisan tinker
```

Then import (run in Tinker):

```php
// Import Students
$students = json_decode(file_get_contents('storage/app/students_export.json'), true);
foreach ($students as $student) {
    \App\Models\Student::create($student);
}

// Import Courses
$courses = json_decode(file_get_contents('storage/app/courses_export.json'), true);
foreach ($courses as $course) {
    \App\Models\Course::create($course);
}

// Import Batches
$batches = json_decode(file_get_contents('storage/app/batches_export.json'), true);
foreach ($batches as $batch) {
    \App\Models\Batch::create($batch);
}

// Import Enrollments
$enrollments = json_decode(file_get_contents('storage/app/enrollments_export.json'), true);
foreach ($enrollments as $enrollment) {
    \App\Models\Enrollment::create($enrollment);
}

// Import Payments
$payments = json_decode(file_get_contents('storage/app/payments_export.json'), true);
foreach ($payments as $payment) {
    \App\Models\Payment::create($payment);
}

// Import Users
$users = json_decode(file_get_contents('storage/app/users_export.json'), true);
foreach ($users as $user) {
    \App\Models\User::create($user);
}

// Import Assessments
$assessments = json_decode(file_get_contents('storage/app/assessments_export.json'), true);
foreach ($assessments as $assessment) {
    \App\Models\Assessment::create($assessment);
}

// Import Assessment Results
$results = json_decode(file_get_contents('storage/app/assessment_results_export.json'), true);
foreach ($results as $result) {
    \App\Models\AssessmentResult::create($result);
}

// Import Certificates
$certificates = json_decode(file_get_contents('storage/app/certificates_export.json'), true);
foreach ($certificates as $certificate) {
    \App\Models\Certificate::create($certificate);
}

exit
```

---

## Method 2: Using Database Seeders (Better for Production)

### Step 1: Create Export Seeder Locally

Create a seeder to export data:

```bash
php artisan make:seeder ExportDataSeeder
```

### Step 2: Export via Seeder

Edit the seeder to export data, then run it.

### Step 3: Import via Seeder on Hostinger

Create import seeder and run it.

---

## Method 3: Direct SQL Export/Import (Advanced)

### Step 1: Export SQLite Data

```bash
# On local Mac
sqlite3 database/database.sqlite .dump > database_export.sql
```

### Step 2: Convert SQLite SQL to MySQL

SQLite and MySQL have different syntax. You'll need to:
- Convert SQLite syntax to MySQL
- Handle data types differences
- Fix foreign key constraints

### Step 3: Import to MySQL

```bash
# On Hostinger
mysql -u your_db_user -p your_database < converted_export.sql
```

---

## Method 4: Fresh Start (Recommended for Production)

If you don't have critical data, start fresh:

```bash
# On Hostinger
cd ~/public_html/v2student
php artisan migrate:fresh
php artisan db:seed --class=AdminUserSeeder
```

Then manually add data through the admin panel.

---

## ⚠️ Important Notes

1. **Foreign Key Order**: Import tables in correct order (courses → batches → students → enrollments, etc.)
2. **ID Conflicts**: May need to reset auto-increment IDs
3. **Timestamps**: SQLite and MySQL handle timestamps differently
4. **Data Types**: Some types may need conversion
5. **Backup First**: Always backup production database before importing

---

## Quick Export Script (Local)

I'll create a script to export all data easily.

---

**Which method would you prefer? I recommend Method 1 (Tinker) for simplicity.**

