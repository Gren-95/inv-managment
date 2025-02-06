DROP DATABASE IF EXISTS it_equipment;
CREATE DATABASE it_equipment;
USE it_equipment;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS equipment_audits;
DROP TABLE IF EXISTS equipment_status_history;
DROP TABLE IF EXISTS write_offs;
DROP TABLE IF EXISTS equipment;
DROP TABLE IF EXISTS equipment_models;
DROP TABLE IF EXISTS equipment_types;
DROP TABLE IF EXISTS shared_accounts;
DROP TABLE IF EXISTS user_permissions;
DROP TABLE IF EXISTS permissions;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS areas;
DROP TABLE IF EXISTS departments;
DROP TABLE IF EXISTS branches;
DROP TABLE IF EXISTS countries;

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
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    password VARCHAR(255) NULL,
    active BOOLEAN NOT NULL DEFAULT TRUE
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
    serial_number VARCHAR(100) NOT NULL UNIQUE,
    buy_year INT NOT NULL,
    warranty_end DATE NOT NULL,
    is_company_owned BOOLEAN DEFAULT TRUE,
    status ENUM('available', 'assigned', 'maintenance', 'written_off', 'pending_write_off') DEFAULT 'available',
    assigned_to_id INT,
    area_id INT,
    teamviewer_id BIGINT,
    cerf_id VARCHAR(20) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (model_id) REFERENCES equipment_models(id),
    FOREIGN KEY (assigned_to_id) REFERENCES users(id),
    FOREIGN KEY (area_id) REFERENCES areas(id),
    last_audit_date TIMESTAMP NULL,
    last_audited_by_id INT,
    FOREIGN KEY (last_audited_by_id) REFERENCES users(id)
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
    id INT PRIMARY KEY AUTO_INCREMENT,
    old_status ENUM('available', 'assigned', 'maintenance', 'written_off') NULL,
    new_status ENUM('available', 'assigned', 'maintenance', 'written_off') NOT NULL,
    equipment_id INT NOT NULL,
    changed_by_user_id INT,
    comment TEXT,
    old_user_id INT,
    new_user_id INT,
    old_location_id INT,
    new_location_id INT,
    old_assigned_to_id INT,
    new_assigned_to_id INT,
    old_teamviewer_id BIGINT NULL,
    new_teamviewer_id BIGINT NULL,
    old_cerf_id VARCHAR(20) NULL,
    new_cerf_id VARCHAR(20) NULL,
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (equipment_id) REFERENCES equipment(id),
    FOREIGN KEY (changed_by_user_id) REFERENCES users(id),
    FOREIGN KEY (old_location_id) REFERENCES areas(id),
    FOREIGN KEY (new_location_id) REFERENCES areas(id),
    FOREIGN KEY (old_assigned_to_id) REFERENCES users(id),
    FOREIGN KEY (new_assigned_to_id) REFERENCES users(id)
);

CREATE TABLE shared_accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    passcode VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Sample data for shared_accounts
INSERT INTO shared_accounts (username, email, passcode) VALUES
('user1', 'user1@example.com', 'passcode1'),
('user2', 'user2@example.com', 'passcode2'),
('user3', 'user3@example.com', 'passcode3'),
('user4', 'user4@example.com', 'passcode4'),
('user5', 'user5@example.com', 'passcode5');

CREATE TABLE equipment_audits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    equipment_id INT NOT NULL,
    serial_number VARCHAR(100) NOT NULL,
    current_status VARCHAR(50) NOT NULL,
    new_status VARCHAR(50) NOT NULL,
    current_location_id INT,
    new_location_id INT,
    current_assigned_to_id INT,
    new_assigned_to_id INT,
    audited_by_user_id INT,
    audit_notes TEXT,
    audit_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    approved_by_user_id INT,
    approval_date TIMESTAMP NULL,
    teamviewer_id BIGINT,
    cerf_id BIGINT,
    FOREIGN KEY (equipment_id) REFERENCES equipment(id),
    FOREIGN KEY (current_location_id) REFERENCES areas(id),
    FOREIGN KEY (new_location_id) REFERENCES areas(id),
    FOREIGN KEY (current_assigned_to_id) REFERENCES users(id),
    FOREIGN KEY (new_assigned_to_id) REFERENCES users(id),
    FOREIGN KEY (audited_by_user_id) REFERENCES users(id),
    FOREIGN KEY (approved_by_user_id) REFERENCES users(id)
);

-- Create permissions table
CREATE TABLE permissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert basic permissions
INSERT INTO permissions (name, description) VALUES
('login', 'Can login to the system'),
('view_equipment', 'Can view equipment list'),
('manage_equipment', 'Can add/edit equipment'),
('write_off_equipment', 'Can write off equipment'),
('manage_users', 'Can manage users'),
('manage_locations', 'Can manage locations'),
('manage_models', 'Can manage models and types'),
('view_audit', 'Can view audit records'),
('perform_audit', 'Can perform equipment audits'),
('approve_audit', 'Can approve/reject audits'),
('manage_shared_accounts', 'Can manage shared accounts');

-- Create user_permissions junction table
CREATE TABLE user_permissions (
    user_id INT NOT NULL,
    permission_id INT NOT NULL,
    PRIMARY KEY (user_id, permission_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Grant all permissions to existing users for backward compatibility
INSERT INTO user_permissions (user_id, permission_id)
SELECT u.id, p.id 
FROM users u 
CROSS JOIN permissions p;

-- Create admin user with password 'admin'
INSERT INTO users (name, email, password, ppid, active) VALUES 
('Admin', 'admin@company.com', '$2y$10$jg.ciCkbFtLggQetN5tYy.dalziJrTvgVi2llL.rW6uaxP2SoIip6', 'admin', TRUE);

-- Grant all permissions to admin user
INSERT INTO user_permissions (user_id, permission_id)
SELECT 
    (SELECT id FROM users WHERE ppid = 'admin'),
    id
FROM permissions;

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
INSERT INTO equipment (model_id, serial_number, buy_year, warranty_end, is_company_owned, status, assigned_to_id, area_id, teamviewer_id, cerf_id) VALUES 
(1, 'MBP2023001', 2023, '2026-01-01', 1, 'assigned', 1, 1, 123456789, 'CERF001'),
(1, 'MBP2023002', 2023, '2026-01-01', 1, 'assigned', 2, 1, 987654321, 'CERF002'),
(2, 'XPS2023001', 2023, '2026-01-01', 1, 'available', NULL, 2, NULL, NULL),
(3, 'OPT2022001', 2022, '2025-01-01', 1, 'maintenance', NULL, 3, 456789123, 'CERF003'),
(4, 'U2720Q2021001', 2021, '2024-01-01', 1, 'assigned', 3, 4, NULL, NULL),
(5, 'HP404DN2022001', 2022, '2025-01-01', 1, 'available', NULL, 5, NULL, 'CERF004'),
(6, 'IP14P2022001', 2022, '2024-01-01', 1, 'assigned', 1, 1, 789123456, 'CERF005'),
(7, 'SGS23001', 2023, '2025-01-01', 1, 'assigned', 2, 2, 321654987, 'CERF006');

-- Sample Write-offs
INSERT INTO write_offs (equipment_id, type, comment) VALUES 
(4, 'broken', 'Hardware failure - motherboard replacement needed');

-- Sample Status History
INSERT INTO equipment_status_history (
    equipment_id, old_status, new_status, 
    old_location_id, new_location_id,
    old_user_id, new_user_id,
    old_teamviewer_id, new_teamviewer_id,
    old_cerf_id, new_cerf_id,
    changed_by_user_id, comment
) VALUES 
(1, 'available', 'assigned', NULL, 1, NULL, 1, NULL, 123456789, NULL, 'CERF001', 1, 'Initial assignment'),
(2, 'available', 'assigned', 2, 1, NULL, 2, 987654320, 987654321, NULL, 'CERF002', 1, 'Reassigned with new TeamViewer'),
(3, 'available', 'maintenance', 3, 3, 2, NULL, 456789123, NULL, 'CERF003', NULL, 2, 'Sent for repair'),
(4, 'assigned', 'maintenance', 4, 3, 3, NULL, NULL, NULL, NULL, NULL, 2, 'Hardware failure'),
(5, 'available', 'assigned', 5, 5, NULL, 1, NULL, NULL, 'CERF004', 'CERF004', 3, 'Assigned to printer room'),
(6, 'available', 'assigned', NULL, 1, NULL, 1, 789123455, 789123456, NULL, 'CERF005', 3, 'New device setup'),
(7, 'assigned', 'maintenance', 2, 2, 2, NULL, 321654987, 321654987, 'CERF006', 'CERF006', 3, 'Temporary maintenance'); 