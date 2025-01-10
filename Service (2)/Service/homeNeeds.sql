-- CREATE DATABASE householdservice;

-- USE householdservice;

CREATE TABLE admin (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(10) NOT NULL,
    address VARCHAR(255) NOT NULL,
    image VARCHAR(255),
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE serviceproviders (
    provider_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    citizenship_no VARCHAR(50) NOT NULL,
    address TEXT NOT NULL,
    image VARCHAR(255),
    service_type VARCHAR(100) NOT NULL,
    profile_description TEXT,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    registration_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status TINYINT(1) NOT NULL DEFAULT 0
);

CREATE TABLE services (
    service_id INT AUTO_INCREMENT PRIMARY KEY,
    service_name VARCHAR(255) NOT NULL,
    description VARCHAR(255) NOT NULL
);

CREATE TABLE bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    service_name VARCHAR(255) NOT NULL,
    provider_name VARCHAR(255) NOT NULL,
    provider_id INT NOT NULL,
    user_name VARCHAR(255) NOT NULL,
    user_email VARCHAR(255) NOT NULL,
    user_phone VARCHAR(20) NOT NULL,
    address VARCHAR(255) NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    location VARCHAR(255),
    user_id INT NOT NULL,
    status ENUM('Pending', 'Confirmed', 'Canceled', 'Completed') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (provider_id) REFERENCES serviceproviders(provider_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);
-- CREATE TABLE provider_schedule (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     provider_id INT NOT NULL,
--     available_date DATE NOT NULL,
--     available_time TIME NOT NULL,
--     status ENUM('available', 'booked') DEFAULT 'available',
--     FOREIGN KEY (provider_id) REFERENCES serviceproviders(provider_id)
-- );

CREATE TABLE provider_schedule (
    schedule_id INT AUTO_INCREMENT PRIMARY KEY,
    provider_id INT NOT NULL,
    available_date DATE NOT NULL,
    available_time TIME NOT NULL,
    is_booked BOOLEAN DEFAULT 0,
    FOREIGN KEY (provider_id) REFERENCES serviceproviders(provider_id)
);


CREATE TABLE reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    provider_id INT NOT NULL,
    booking_id INT NOT NULL,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    review TEXT NOT NULL,
    review_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (provider_id) REFERENCES serviceproviders(provider_id),
    FOREIGN KEY (booking_id) REFERENCES bookings(booking_id)
);