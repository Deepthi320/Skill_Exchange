-- Create Database
CREATE DATABASE IF NOT EXISTS skill_exchange_db;
USE skill_exchange_db;

-- 1. Users Table
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Skills Table
CREATE TABLE IF NOT EXISTS skills (
    skill_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    skill_name VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    skill_level ENUM('Beginner', 'Intermediate', 'Expert') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- 3. Requests Table
CREATE TABLE IF NOT EXISTS requests (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    learner_id INT NOT NULL,
    skill_id INT NOT NULL,
    request_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Pending', 'Accepted', 'Rejected') DEFAULT 'Pending',
    FOREIGN KEY (learner_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (skill_id) REFERENCES skills(skill_id) ON DELETE CASCADE
);

-- Insert Sample Users (Password for all is 'password123')
INSERT INTO users (user_id, name, email, password) VALUES
(1, 'Rahul Sharma', 'rahul@gmail.com', '$2y$10$8W3Y6a.Z6XbXg6pQ1Z9Yee7v6fE5K3M2vXWb6v7Y6zX8x5K6Q1Z2.'),
(2, 'Priya Patel', 'priya@gmail.com', '$2y$10$8W3Y6a.Z6XbXg6pQ1Z9Yee7v6fE5K3M2vXWb6v7Y6zX8x5K6Q1Z2.'),
(3, 'Amit Verma', 'amit@gmail.com', '$2y$10$8W3Y6a.Z6XbXg6pQ1Z9Yee7v6fE5K3M2vXWb6v7Y6zX8x5K6Q1Z2.');

-- Insert Sample Skills
INSERT INTO skills (skill_id, user_id, skill_name, description, skill_level) VALUES
(1, 1, 'Web Development', 'Can teach HTML, CSS, JavaScript, and basic PHP routing.', 'Intermediate'),
(2, 2, 'Python Programming', 'Data structures, algorithms, and automated scripting using Python.', 'Expert'),
(3, 3, 'Graphic Design', 'UI design, logo creation, and vector illustrations using Figma/Illustrator.', 'Beginner');

-- Insert Sample Requests
INSERT INTO requests (request_id, learner_id, skill_id, status) VALUES
(1, 2, 1, 'Pending'),
(2, 3, 2, 'Accepted');