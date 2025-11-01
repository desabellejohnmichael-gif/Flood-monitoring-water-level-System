CREATE DATABASE flood_monitoring;
USE flood_monitoring;

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE sensor_data (
    id INT PRIMARY KEY AUTO_INCREMENT,
    water_level FLOAT NOT NULL,
    temperature FLOAT,
    humidity FLOAT,
    rainfall FLOAT,
    status ENUM('normal', 'warning', 'danger') DEFAULT 'normal',
    location VARCHAR(100),
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE alerts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sensor_data_id INT,
    alert_type ENUM('warning', 'danger') NOT NULL,
    message TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sensor_data_id) REFERENCES sensor_data(id)
);

-- Insert default admin user (password: admin123)
INSERT INTO users (username, password, role) VALUES ('admin', '$2y$10$8K1p/bJZbHx5O6VE9etKXOqPZx6GfC2k.1oR2Hm4hKI6UZGqXINdi', 'admin');