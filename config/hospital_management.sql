-- Create database
CREATE DATABASE IF NOT EXISTS hospital_management;
USE hospital_management;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'receptionist', 'doctor', 'patient') NOT NULL,
    phone VARCHAR(20),
    specialization VARCHAR(100),
    department_id INT,
    status ENUM('active', 'inactive', 'on_leave') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Departments table
CREATE TABLE IF NOT EXISTS departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Appointments table
CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    date DATE NOT NULL,
    time TIME NOT NULL,
    reason TEXT,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES users(id),
    FOREIGN KEY (doctor_id) REFERENCES users(id)
);

-- Notifications table
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type VARCHAR(50),
    message TEXT,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Insert default admin (password: admin123)
INSERT INTO users (name, email, password, role) 
VALUES ('Admin User', 'admin@hospital.com', '$2y$10$8pzW5HkR8sF9wYq7Xv6ZWeLbJg8KdN2Q3RtS4Uv5WxYzA1BcDeFgH', 'admin');

-- Insert sample doctor (password: doctor123)
INSERT INTO users (name, email, password, role, specialization) 
VALUES ('Dr. Ahmed Ali', 'doctor@hospital.com', '$2y$10$8pzW5HkR8sF9wYq7Xv6ZWeLbJg8KdN2Q3RtS4Uv5WxYzA1BcDeFgH', 'doctor', 'Cardiology');

-- Insert sample receptionist (password: reception123)
INSERT INTO users (name, email, password, role) 
VALUES ('Receptionist User', 'reception@hospital.com', '$2y$10$8pzW5HkR8sF9wYq7Xv6ZWeLbJg8KdN2Q3RtS4Uv5WxYzA1BcDeFgH', 'receptionist');

-- Insert sample patient (password: patient123)
INSERT INTO users (name, email, password, role, phone) 
VALUES ('Patient User', 'patient@hospital.com', '$2y$10$8pzW5HkR8sF9wYq7Xv6ZWeLbJg8KdN2Q3RtS4Uv5WxYzA1BcDeFgH', 'patient', '+201234567890');

-- Insert departments
INSERT INTO departments (name, description) VALUES
('Cardiology', 'Heart and cardiovascular system'),
('Neurology', 'Brain and nervous system'),
('Orthopedics', 'Bones and musculoskeletal system');

-- Insert sample appointment
INSERT INTO appointments (patient_id, doctor_id, date, time, reason, status) 
VALUES (4, 2, '2024-01-15', '10:00:00', 'Regular checkup', 'confirmed');