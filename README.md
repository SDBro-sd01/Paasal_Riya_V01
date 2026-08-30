# Paasal Riya V01

A simple PHP and MySQL web system for finding and managing school transport services.

This project is made with PHP, MySQL, HTML, CSS, JavaScript, Bootstrap, Font Awesome and PHP dotenv.

The project has user login, user registration, user management, transport service management, service images, required documents, schedules, schools, ratings, favorites, comments, messages, reports and audit logs.

## Project Repository

GitHub: https://github.com/SDBro-sd01/Paasal_Riya_V01

## Main Technologies

PHP

MySQL

HTML

CSS

JavaScript

Bootstrap

Font Awesome

Composer

PHP dotenv

## Main Features

1. User registration and login
2. User account management
3. Admin user management
4. Password change and password reset support
5. Transport service registration
6. Bus, van and three wheeler service types
7. School transport and private institute transport
8. Service approval, rejection and hold status
9. Service image upload
10. Service document upload
11. Service telephone numbers
12. Service email addresses
13. Service websites
14. Schools connected to services
15. Service schedules
16. Favorites
17. Ratings from 1 to 5
18. Comments and replies
19. User messaging
20. Service reports
21. User audit log
22. General audit log
23. About page content
24. Contact page content
25. Team member details
26. Required document list

## Project Structure

```text
Paasal_Riya_V01/
│
├── Assets/
├── Cookie_Managements/
├── Documents/
│   └── MYSQL BackUp 2026 08 30/
│       └── Dump20260830.sql
├── Includes/
│   ├── db_connection.php
│   ├── audit_helper.php
│   ├── log_in_inc.php
│   ├── sign_up_inc.php
│   └── other backend files
├── Languages_Files/
├── Styles/
├── components/
├── index.php
├── log_in.php
├── log_out.php
├── sign_up.php
├── user_management.php
├── side_bar.php
├── add_and_edit_post.php
├── user_posts.php
├── user_settings.php
└── .gitignore
```

The project database backup is already inside the repository under the Documents folder.

## Requirements

Use the following software on the computer.

1. XAMPP or another PHP server package
2. Apache
3. MySQL
4. PHP
5. Composer
6. A modern web browser
7. phpMyAdmin is recommended for easy database setup

MySQL 8 is recommended because the included database backup was made using MySQL 8.0 tools and contains MySQL 8 style collation settings.

## Step 1: Get the Project

You can download the project from GitHub or clone it with Git.

### Git method

```bash
git clone https://github.com/SDBro-sd01/Paasal_Riya_V01.git
```

After that, open the project folder.

```bash
cd Paasal_Riya_V01
```

You can also use the GitHub Download ZIP option and extract the ZIP file.

## Step 2: Put the Project in the Server Folder

For XAMPP, copy the project folder into:

```text
C:\xampp\htdocs\
```

The final path should look like this.

```text
C:\xampp\htdocs\Paasal_Riya_V01\
```

The main file should be here.

```text
C:\xampp\htdocs\Paasal_Riya_V01\index.php
```

## Step 3: Start Apache and MySQL

Open XAMPP Control Panel.

Start:

```text
Apache
MySQL
```

Both services should show as running.

## Step 4: Create the Database

Open phpMyAdmin.

```text
http://localhost/phpmyadmin
```

Create a new database with this exact name.

```sql
CREATE DATABASE IF NOT EXISTS paasal_riya_db_01
CHARACTER SET utf8mb4
COLLATE utf8mb4_0900_ai_ci;
```

Then select the database.

```sql
USE paasal_riya_db_01;
```

Important: The SQL backup in this project does not create the database itself. It mainly contains the tables and data. So create the database first.

## Step 5: Import the Database

The database backup is here.

```text
Documents/MYSQL BackUp 2026 08 30/Dump20260830.sql
```

In phpMyAdmin:

1. Select `paasal_riya_db_01`
2. Open the Import section
3. Select `Dump20260830.sql`
4. Start the import
5. Wait until the import is complete

The backup contains the database tables and sample data used by the project.

## Step 6: Composer Setup

The project uses PHP dotenv. The database connection file loads Composer autoload and then loads the `.env` file.

The current database connection file uses:

```php
require_once __DIR__ . '/../vendor/autoload.php';
```

This means the `vendor` folder must be in the project root.

Correct:

```text
Paasal_Riya_V01/
├── vendor/
├── .env
├── index.php
└── Includes/
    └── db_connection.php
```

Do not put the `vendor` folder inside `Includes` when using the current `db_connection.php`.

If the `vendor` folder is not available, open Command Prompt inside the project folder and run:

```bash
composer require vlucas/phpdotenv
```

After this, a `vendor` folder and Composer files will be created.

## Step 7: Create the .env File

Create a file named:

```text
.env
```

Put it in the same folder as `index.php`.

Example:

```env
DB_HOST=localhost
DB_USERNAME=root
DB_PASSWORD=
DB_NAME=paasal_riya_db_01
```

For the normal XAMPP MySQL setup, the MySQL username is often `root` and the password may be empty. Change these values when your MySQL setup is different.

The current project database connection reads these four values from `.env`.

## Step 8: Check the Database Connection

The project already has a database connection file here:

```text
Includes/db_connection.php
```

The current file uses MySQLi and reads:

```text
DB_HOST
DB_USERNAME
DB_PASSWORD
DB_NAME
```

It also sets the character set to `utf8mb4`.

## Step 9: Open the Project

Open this address in your browser:

```text
http://localhost/Paasal_Riya_V01/
```

The main page is `index.php`.

You can also open the login page directly.

```text
http://localhost/Paasal_Riya_V01/log_in.php
```

## Database Name

The database name used by the project is:

```text
paasal_riya_db_01
```

The SQL dump also identifies this database name at the top of the file.

## Database Tables

The current SQL backup contains these 30 tables.

```text
1. about_content
2. about_sections
3. audit_log
4. comments
5. contact_content
6. contact_emails
7. contact_phones
8. contact_social_links
9. contact_website_links
10. favorites
11. post_messages
12. post_status_log
13. posts
14. ratings
15. report_options
16. reports
17. req_documents
18. service_assistants
19. service_document_images
20. service_emails
21. service_images
22. service_schedules
23. service_schools
24. service_telephones
25. service_websites
26. services
27. team_members
28. user_audit_log
29. user_created_method
30. users
```

## Important Main Tables

### users

Stores user accounts and user details.

Main fields include:

```text
user_id
username
fullname
mobile
email
nic
district
province
address
user_type
password
created_at
```

The database uses unique values for username, email and NIC.

### services

Stores transport service details.

Important fields include:

```text
service_id
user_id
service_name
reg_no
vehicle_type
service_type
owner
driver
driver_reg_no
province
district
home_town
areas_covered
address
description
road_description
has_morning
morning_place
morning_time
has_evening
evening_place
evening_time
status
edited_after_approval
```

### service_images

Stores service image paths and image order.

### service_document_images

Stores uploaded service document image paths.

### service_schedules

Stores places and times for a service.

### service_schools

Stores schools connected to a transport service.

### service_telephones

Stores service telephone numbers.

### service_emails

Stores service email addresses.

### service_websites

Stores service website links.

### ratings

Stores user ratings from 1 to 5 for services.

### favorites

Stores services saved by users.

### comments

Stores comments and comment replies.

### post_messages

Stores messages between users about services.

### reports

Stores reports sent about services.

### report_options

Stores the available report reasons.

### post_status_log

Stores service status changes such as approved, rejected and hold.

### audit_log

Stores important database changes.

### user_audit_log

Stores important user account actions.

### user_created_method

Stores how a user account was created.

## Useful MySQL Queries

### Select the database

```sql
USE paasal_riya_db_01;
```

### Show all tables

```sql
SHOW TABLES;
```

### Check the users table

```sql
SELECT * FROM users;
```

### Check only basic user details

```sql
SELECT user_id, username, fullname, email, user_type, created_at
FROM users;
```

### Check all services

```sql
SELECT * FROM services;
```

### Show only approved services

```sql
SELECT *
FROM services
WHERE status = 'approved';
```

### Show pending services

```sql
SELECT *
FROM services
WHERE status = 'pending';
```

### Count users

```sql
SELECT COUNT(*) AS total_users
FROM users;
```

### Count services

```sql
SELECT COUNT(*) AS total_services
FROM services;
```

### Count approved services

```sql
SELECT COUNT(*) AS approved_services
FROM services
WHERE status = 'approved';
```

### Find one user by username

```sql
SELECT *
FROM users
WHERE username = 'admin';
```

### Find services owned by one user

```sql
SELECT *
FROM services
WHERE user_id = 1;
```

### Show ratings with service information

```sql
SELECT
    r.rating_id,
    r.rating,
    r.user_id,
    r.service_id,
    s.service_name,
    r.created_at
FROM ratings r
INNER JOIN services s
    ON r.service_id = s.service_id;
```

### Show favorite services

```sql
SELECT
    f.user_id,
    f.service_id,
    s.service_name,
    f.created_at
FROM favorites f
INNER JOIN services s
    ON f.service_id = s.service_id;
```

### Show service comments

```sql
SELECT
    c.comment_id,
    c.user_id,
    c.service_id,
    c.comment,
    c.created_at
FROM comments c
ORDER BY c.created_at DESC;
```

### Show service schedules

```sql
SELECT
    ss.schedule_id,
    ss.service_id,
    ss.label,
    ss.place,
    ss.time
FROM service_schedules ss
ORDER BY ss.service_id, ss.sort_order;
```

### Check the structure of a table

```sql
DESCRIBE users;
```

You can use the same command for another table.

```sql
DESCRIBE services;
```

### Check the database character set

```sql
SELECT
    DEFAULT_CHARACTER_SET_NAME,
    DEFAULT_COLLATION_NAME
FROM information_schema.SCHEMATA
WHERE SCHEMA_NAME = 'paasal_riya_db_01';
```

## Reset the Database for a New Setup

Use this only when you want to remove the current database and import the backup again.

```sql
DROP DATABASE IF EXISTS paasal_riya_db_01;

CREATE DATABASE paasal_riya_db_01
CHARACTER SET utf8mb4
COLLATE utf8mb4_0900_ai_ci;

USE paasal_riya_db_01;
```

After that, import `Dump20260830.sql` again.

## Important Note About the SQL Backup

The SQL backup contains real test data from the current project database. This includes sample users, services, comments, ratings, favorites and audit records.

The backup also contains uploaded file paths such as service images and team member images. After copying the project to another computer, make sure the upload folders used by those paths are also present in the project files.

## Important Note About Passwords

Passwords in the database are stored as password hashes, not plain text passwords.

Do not change a password by putting a normal text password directly into the `password` column.

Use the project password change or password reset feature, or create a proper PHP password hash before changing a password in the database.

## Common Problems and Fixes

### Database connection failed

Check the `.env` file.

Make sure these values are correct.

```env
DB_HOST=localhost
DB_USERNAME=root
DB_PASSWORD=
DB_NAME=paasal_riya_db_01
```

Also make sure MySQL is running in XAMPP.

### Class Dotenv not found

This usually means Composer files are missing.

Run:

```bash
composer require vlucas/phpdotenv
```

Then make sure this file exists.

```text
Paasal_Riya_V01/vendor/autoload.php
```

### Cannot find vendor/autoload.php

The current project code expects:

```text
Paasal_Riya_V01/vendor/autoload.php
```

Make sure the `vendor` folder is in the project root.

### SQL import gives a collation error

The included SQL file uses MySQL 8 style collation settings such as:

```text
utf8mb4_0900_ai_ci
```

MySQL 8 is recommended for this project.

If you are using an older MySQL version or another database server, the SQL file may need a small collation change before import.

### Page does not open

Make sure the project is inside the Apache web folder.

For XAMPP:

```text
C:\xampp\htdocs\Paasal_Riya_V01\
```

Also make sure Apache is running.

Then open:

```text
http://localhost/Paasal_Riya_V01/
```

### Images are not showing

Check that the related upload folders are still inside the project.

The SQL file stores paths such as:

```text
uploads/...
```

The database path and the real folder path should match.

## GitHub Update Commands

After making changes to the project, these commands can be used to update GitHub.

```bash
git status

git add .

git commit -m "Update project"

git push origin main
```

Remember that `.env` and `vendor/` are ignored by the current `.gitignore` file.

Do not upload private database passwords or other private values into GitHub.

## Backup

Before making major database changes, create a fresh MySQL backup.

You can use phpMyAdmin:

```text
phpMyAdmin → paasal_riya_db_01 → Export
```

Save the SQL file in a safe place.

## Quick Setup

For a new computer, the full setup order is:

```text
1. Install XAMPP
2. Start Apache and MySQL
3. Clone or download Paasal_Riya_V01
4. Copy it into C:\xampp\htdocs\
5. Create paasal_riya_db_01
6. Import Dump20260830.sql
7. Create the root .env file
8. Add the correct database details
9. Make sure vendor/autoload.php exists
10. Open http://localhost/Paasal_Riya_V01/
```

## Final Check

Before using the system, check these items.

```text
[ ] Apache is running
[ ] MySQL is running
[ ] Project is inside htdocs
[ ] Database paasal_riya_db_01 exists
[ ] SQL backup was imported
[ ] .env is in the project root
[ ] vendor/autoload.php exists
[ ] DB_HOST is correct
[ ] DB_USERNAME is correct
[ ] DB_PASSWORD is correct
[ ] DB_NAME is paasal_riya_db_01
[ ] Browser can open the project
```

## License

This project is created for learning and project development purposes.

## Author

SDBro

GitHub: https://github.com/SDBro-sd01

## Project Link

https://github.com/SDBro-sd01/Paasal_Riya_V01
