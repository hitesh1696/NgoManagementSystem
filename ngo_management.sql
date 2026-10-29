CREATE DATABASE IF NOT EXISTS ngo_management
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
USE ngo_management;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS activities;
DROP TABLE IF EXISTS donations;
DROP TABLE IF EXISTS volunteers;
DROP TABLE IF EXISTS beneficiaries;
DROP TABLE IF EXISTS admins;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE admins (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE beneficiaries (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    age TINYINT UNSIGNED NOT NULL,
    gender ENUM('Female', 'Male', 'Other') NOT NULL,
    contact_number VARCHAR(30) NOT NULL,
    address VARCHAR(255) NOT NULL,
    registration_date DATE NOT NULL
) ENGINE=InnoDB;

CREATE TABLE volunteers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(30) NOT NULL,
    skills VARCHAR(255) NOT NULL,
    joining_date DATE NOT NULL
) ENGINE=InnoDB;

CREATE TABLE donations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    donor_name VARCHAR(120) NOT NULL,
    amount DECIMAL(12, 2) NOT NULL,
    donation_date DATE NOT NULL,
    payment_method ENUM('Cash', 'Bank Transfer', 'UPI', 'Cheque', 'Other') NOT NULL,
    purpose VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE activities (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    activity_name VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    event_date DATE NOT NULL,
    location VARCHAR(180) NOT NULL,
    status ENUM('Planned', 'Ongoing', 'Completed', 'Cancelled') NOT NULL DEFAULT 'Planned'
) ENGINE=InnoDB;

INSERT INTO admins (username, password, full_name) VALUES
('admin', '$2y$10$vaw7rYlrcOicSFfkB8LCtOSN5A8OOEj2Kc9a.uCAFD2eEbb3YlQy6', 'NGO Administrator');

INSERT INTO beneficiaries (name, age, gender, contact_number, address, registration_date) VALUES
('Asha Kumari', 34, 'Female', '9876543210', 'Ward 4, Jaipur', '2026-01-12'),
('Ravi Kumar', 42, 'Male', '9876501234', 'Shanti Nagar, Jaipur', '2026-02-04'),
('Meena Devi', 27, 'Female', '9812345670', 'Civil Lines, Jaipur', '2026-03-18');

INSERT INTO volunteers (name, email, phone, skills, joining_date) VALUES
('Priya Sharma', 'priya@example.com', '9876512345', 'Teaching, mentoring', '2026-01-15'),
('Arjun Singh', 'arjun@example.com', '9876523456', 'Healthcare, first aid', '2026-02-10'),
('Neha Verma', 'neha@example.com', '9876534567', 'Fundraising, outreach', '2026-03-02');

INSERT INTO donations (donor_name, amount, donation_date, payment_method, purpose) VALUES
('Community Trust', 25000.00, '2026-01-20', 'Bank Transfer', 'Education support'),
('Anita Patel', 5000.00, '2026-02-15', 'UPI', 'Food supplies'),
('Local Business Group', 12000.00, '2026-03-10', 'Cheque', 'Community health camp');

INSERT INTO activities (activity_name, description, event_date, location, status) VALUES
('Community Health Camp', 'Free health checks and basic consultations.', '2026-04-12', 'Community Hall, Jaipur', 'Completed'),
('After-school Learning', 'Weekly learning support for school-age children.', '2026-05-04', 'Ward 4 Learning Centre', 'Ongoing'),
('Food Distribution Drive', 'Distribute essential groceries to local families.', '2026-06-18', 'Shanti Nagar, Jaipur', 'Planned');
