# NGO Management System

A beginner-friendly NGO administration project built with core PHP, MySQL, HTML, CSS and Bootstrap 5. It provides session-protected management pages for beneficiaries, volunteers, donations and community activities.

## Project abstract

The NGO Management System is a web-based application that helps a small non-governmental organization maintain common operational records in one place. An administrator can sign in to review a dashboard and create, view, update or remove records for beneficiaries, volunteers, donations and activities. MySQL stores the data, while PHP handles authentication, validation and database operations.

## Objectives

- Provide a simple, centralized way to manage NGO records.
- Secure management pages behind an administrator login.
- Summarize beneficiary, volunteer, donation and activity information.
- Demonstrate PHP sessions, MySQLi prepared statements, password hashing and CSRF protection.
- Keep installation and source code approachable for a college project.

## Modules

- **Admin login:** session-based login and logout, with passwords verified using `password_verify()`.
- **Dashboard:** counts beneficiaries, volunteers and activities, and sums donation amounts.
- **Beneficiaries:** add, list, edit and delete beneficiary details.
- **Volunteers:** add, list, edit and delete volunteer details.
- **Donations:** record, list and delete donations; shows the total received.
- **Activities:** add, list, edit and delete events, including their status.

All records are accessed with MySQLi prepared statements. Forms that change records include CSRF tokens, required fields are validated on the server, and displayed data is HTML-escaped.

## Requirements

- XAMPP or WampServer with Apache, PHP 8.0+ and MySQL/MariaDB
- A web browser
- Internet access for the Bootstrap 5 stylesheet and script CDN (the app's backend and database run locally)

## Install and run with XAMPP

1. Copy the `NGOManagementSystem` folder into `C:\xampp\htdocs\`.
2. Start **Apache** and **MySQL** from the XAMPP Control Panel.
3. Open `http://localhost/phpmyadmin`.
4. Choose **Import**, select `C:\xampp\htdocs\NGOManagementSystem\ngo_management.sql`, and run the import. The SQL file creates the `ngo_management` database, tables and sample records. Importing it again resets these tables and deletes existing records.
5. If your MySQL credentials differ from the XAMPP defaults, edit the host, username, password and database name in `config/db.php`. The default configuration uses `localhost`, user `root`, an empty password, and database `ngo_management`.
6. Visit `http://localhost/NGOManagementSystem/`.
7. Sign in with the local demo account:

   - **Username:** `admin`
   - **Password:** `admin123`

Change the demo password before using the application with real or sensitive data. This project is intended for local demonstration and does not include production deployment hardening.

## Install and run with WampServer

1. Copy the `NGOManagementSystem` folder into WampServer's `www` directory (commonly `C:\wamp64\www\`).
2. Start WampServer and make sure its Apache and MySQL/MariaDB services are running.
3. Open WampServer's phpMyAdmin from its tray menu (or visit `http://localhost/phpmyadmin`).
4. Import `C:\wamp64\www\NGOManagementSystem\ngo_management.sql`. The SQL file creates the `ngo_management` database, tables and sample records. Importing it again resets these tables and deletes existing records.
5. If your MySQL credentials differ from the defaults, update the host, username, password and database name in `config/db.php`.
6. Visit `http://localhost/NGOManagementSystem/` and sign in with username `admin` and password `admin123`.

The project is compatible with WampServer when PHP 8.0 or later and the `mysqli` extension are enabled. The app expects the project folder to be named `NGOManagementSystem` and located under the web root, because its navigation and asset URLs use `/NGOManagementSystem/`. Bootstrap is loaded from a CDN, so internet access is needed for its styling.

## Project structure

```text
NGOManagementSystem/
├── activities/
│   └── index.php
├── assets/
│   └── css/
│       └── style.css
├── beneficiaries/
│   └── index.php
├── config/
│   └── db.php
├── donations/
│   └── index.php
├── includes/
│   ├── footer.php
│   ├── functions.php
│   ├── header.php
│   └── sidebar.php
├── volunteers/
│   └── index.php
├── dashboard.php
├── index.php
├── login.php
├── logout.php
├── ngo_management.sql
└── README.md
```

## Database tables

The SQL import creates `admins`, `beneficiaries`, `volunteers`, `donations` and `activities`. The supplied sample records make the dashboard and module tables ready to demonstrate immediately.

## Notes

- Database credentials are configured in `config/db.php`.
- Bootstrap styling and its responsive components are loaded from jsDelivr; access to that CDN is needed for the full Bootstrap appearance.
- Use the sample credentials only on a local development setup.
