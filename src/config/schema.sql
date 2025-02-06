DROP DATABASE IF EXISTS it_equipment;
CREATE DATABASE it_equipment;
USE it_equipment;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS equipment_status_history;
DROP TABLE IF EXISTS write_offs;
DROP TABLE IF EXISTS equipment;
DROP TABLE IF EXISTS equipment_models;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS areas;
DROP TABLE IF EXISTS departments;
DROP TABLE IF EXISTS branches;
DROP TABLE IF EXISTS countries;
DROP TABLE IF EXISTS equipment_types;
DROP TABLE IF EXISTS shared_accounts;

SET FOREIGN_KEY_CHECKS = 1;

-- First, create the base table for locations
CREATE TABLE equipment_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    lifespan_years INT NOT NULL DEFAULT 5
);

CREATE TABLE countries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

-- Branches belong to countries
CREATE TABLE branches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    country_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    FOREIGN KEY (country_id) REFERENCES countries(id),
    UNIQUE KEY unique_branch (country_id, name)
);

-- Departments belong to branches
CREATE TABLE departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    branch_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    FOREIGN KEY (branch_id) REFERENCES branches(id),
    UNIQUE KEY unique_department (branch_id, name)
);

-- Areas belong to departments
CREATE TABLE areas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    department_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    FOREIGN KEY (department_id) REFERENCES departments(id),
    UNIQUE KEY unique_area (department_id, name)
);

-- Users belong to areas
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    ppid VARCHAR(50) UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE equipment_models (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type_id INT NOT NULL,
    name VARCHAR(200) NOT NULL,
    release_year INT NOT NULL,
    FOREIGN KEY (type_id) REFERENCES equipment_types(id)
);

CREATE TABLE equipment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    model_id INT NOT NULL,
    serial_number VARCHAR(100) UNIQUE,
    buy_year INT NOT NULL,
    warranty_end DATE NOT NULL,
    is_company_owned BOOLEAN DEFAULT TRUE,
    status ENUM('available', 'assigned', 'maintenance', 'written_off', 'pending_write_off') DEFAULT 'available',
    assigned_to_id INT,
    area_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (model_id) REFERENCES equipment_models(id),
    FOREIGN KEY (assigned_to_id) REFERENCES users(id),
    FOREIGN KEY (area_id) REFERENCES areas(id)
);

CREATE TABLE write_offs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    equipment_id INT NOT NULL,
    type ENUM('charity', 'broken', 'lost', 'sold', 'recycled', 'other') NOT NULL,
    comment TEXT NOT NULL,
    write_off_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (equipment_id) REFERENCES equipment(id)
);

CREATE TABLE equipment_status_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    equipment_id INT NOT NULL,
    old_status ENUM('available', 'assigned', 'maintenance', 'written_off', 'pending_write_off'),
    new_status ENUM('available', 'assigned', 'maintenance', 'written_off', 'pending_write_off') NOT NULL,
    changed_by_user_id INT,
    comment TEXT,
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (equipment_id) REFERENCES equipment(id),
    FOREIGN KEY (changed_by_user_id) REFERENCES users(id)
);

CREATE TABLE shared_accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    passcode VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Sample Equipment Types
INSERT INTO equipment_types (name, lifespan_years) VALUES 
('Laptop', 4),
('Desktop', 5),
('Monitor', 5),
('Printer', 4),
('Mobile Phone', 2),
('Tablet', 3);

-- Sample Countries
INSERT INTO countries (name) VALUES 
('United States'),
('United Kingdom'),
('Germany'),
('France'),
('Spain');

-- Sample Branches
INSERT INTO branches (country_id, name) VALUES 
(1, 'New York Office'),
(1, 'San Francisco Office'),
(2, 'London Office'),
(3, 'Berlin Office'),
(4, 'Paris Office');

-- Sample Departments
INSERT INTO departments (branch_id, name) VALUES 
(1, 'Engineering'),
(1, 'Sales'),
(1, 'Marketing'),
(2, 'Engineering'),
(2, 'HR'),
(3, 'Engineering'),
(3, 'Finance');

-- Sample Areas
INSERT INTO areas (department_id, name) VALUES 
(1, 'Software Development'),
(1, 'DevOps'),
(1, 'QA'),
(2, 'Enterprise Sales'),
(2, 'SMB Sales'),
(3, 'Digital Marketing'),
(4, 'Backend Team'),
(4, 'Frontend Team');

-- Sample Users
INSERT INTO users (name, email, ppid) VALUES 
('John Doe', 'john.doe@company.com', 'PP100001'),
('Jane Smith', 'jane.smith@company.com', 'PP100002'),
('Bob Wilson', 'bob.wilson@company.com', 'PP100003'),
('Alice Brown', 'alice.brown@company.com', 'PP100004'),
('Charlie Davis', 'charlie.davis@company.com', 'PP100005');

-- Sample Equipment Models
INSERT INTO equipment_models (type_id, name, release_year) VALUES 
(1, 'MacBook Pro 16"', 2023),
(1, 'Dell XPS 15', 2023),
(2, 'Dell OptiPlex 7090', 2022),
(3, 'Dell U2720Q', 2021),
(4, 'HP LaserJet Pro M404dn', 2022),
(5, 'iPhone 14 Pro', 2022),
(5, 'Samsung Galaxy S23', 2023);

-- Sample Equipment
INSERT INTO equipment (model_id, serial_number, buy_year, warranty_end, is_company_owned, status, assigned_to_id, area_id) VALUES 
(1, 'MBP2023001', 2023, '2026-01-01', 1, 'assigned', 1, 1),
(1, 'MBP2023002', 2023, '2026-01-01', 1, 'assigned', 2, 1),
(2, 'XPS2023001', 2023, '2026-01-01', 1, 'available', NULL, 2),
(3, 'OPT2022001', 2022, '2025-01-01', 1, 'maintenance', NULL, 3),
(4, 'U2720Q2021001', 2021, '2024-01-01', 1, 'assigned', 3, 4),
(5, 'HP404DN2022001', 2022, '2025-01-01', 1, 'available', NULL, 5),
(6, 'IP14P2022001', 2022, '2024-01-01', 1, 'assigned', 1, 1),
(7, 'SGS23001', 2023, '2025-01-01', 1, 'assigned', 2, 2);

-- Sample Write-offs
INSERT INTO write_offs (equipment_id, type, comment) VALUES 
(4, 'broken', 'Hardware failure - motherboard replacement needed');

-- Sample Status History
INSERT INTO equipment_status_history (equipment_id, old_status, new_status, changed_by_user_id, comment) VALUES 
(1, 'available', 'assigned', 1, 'Assigned to John Doe'),
(2, 'available', 'assigned', 1, 'Assigned to Jane Smith'),
(4, 'available', 'maintenance', 2, 'Sent for repair'),
(6, 'available', 'assigned', 3, 'Assigned to John Doe'),
(7, 'available', 'assigned', 3, 'Assigned to Jane Smith'); 