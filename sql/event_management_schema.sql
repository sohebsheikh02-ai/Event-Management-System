-- Event Management System Database Schema
CREATE DATABASE IF NOT EXISTS event_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE event_management;

-- Users
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  phone VARCHAR(20),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Admin
CREATE TABLE admin (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Events
CREATE TABLE events (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  description TEXT,
  event_date DATE NOT NULL,
  event_time TIME NOT NULL,
  venue VARCHAR(200) NOT NULL,
  category VARCHAR(80),
  price DECIMAL(10,2) DEFAULT 0.00,
  image VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Registrations
CREATE TABLE registrations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  event_id INT NOT NULL,
  registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_user_event (user_id, event_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Invoices
CREATE TABLE invoices (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  event_id INT NOT NULL,
  registration_id INT NOT NULL,
  invoice_number VARCHAR(50) NOT NULL UNIQUE,
  amount DECIMAL(10,2) NOT NULL,
  status ENUM('Paid','Free','Pending') DEFAULT 'Paid',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
  FOREIGN KEY (registration_id) REFERENCES registrations(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Seed data
INSERT INTO admin (username, password) VALUES
('admin', '$2y$10$e0NRpQ9oM0YwQ8h0g3y0xeXr7m5cQ3yY5nQ8m6R2zJ1kV4bH9fA1G'); -- admin123

INSERT INTO users (name, email, password, phone) VALUES
('John Doe', 'john@example.com', '$2y$10$wH3jN5bV3rK7yY2qX4tL5O8c0qR1pS6mT9nU2vW4xZ6aB8dC0eF2G', '9876543210'); -- password123

INSERT INTO events (title, description, event_date, event_time, venue, category, price) VALUES
('Tech Conference 2025', 'Annual tech conference with industry leaders.', '2025-12-15', '09:00:00', 'Convention Center, Mumbai', 'Technology', 1500.00),
('Music Fest', 'Live music night with top artists.', '2025-11-20', '18:00:00', 'Open Grounds, Delhi', 'Music', 800.00),
('Startup Meetup', 'Networking event for founders & investors.', '2025-10-30', '17:00:00', 'WeWork, Bangalore', 'Business', 0.00),
('AI Workshop', 'Hands-on workshop on Generative AI.', '2025-12-05', '10:00:00', 'IIT Auditorium, Chennai', 'Education', 500.00);
