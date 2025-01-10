-- CREATE DATABASE HouseholdServiceDB;
-- USE HouseholdServiceDB;

CREATE TABLE admin (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);
CREATE TABLE serviceProviders (
    provider_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(15),
    citizenship_no VARCHAR(20),
    address TEXT,
    image VARCHAR(255),
    service_type VARCHAR(100),
    profile_description TEXT,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status TINYINT(1) DEFAULT 0
);
CREATE TABLE services (
    service_id INT AUTO_INCREMENT PRIMARY KEY,
    service_name VARCHAR(255) NOT NULL,
    description VARCHAR(255) NOT NULL
);
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(15),
    address TEXT,
    image VARCHAR(255),
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8)
);
CREATE TABLE bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    provider_id INT,
    service_id INT,
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Pending', 'Approved', 'Declined') DEFAULT 'Pending',
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (provider_id) REFERENCES serviceProviders(provider_id),
    FOREIGN KEY (service_id) REFERENCES services(service_id)
);
CREATE TABLE reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    provider_id INT,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    review TEXT,
    review_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (provider_id) REFERENCES serviceProviders(provider_id)
);
CREATE TABLE inquiries (
    inquiry_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    provider_id INT,
    inquiry TEXT,
    inquiry_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    response TEXT,
    response_date TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (provider_id) REFERENCES serviceProviders(provider_id)
);

-- CREATE TABLE provider_schedule (
--     schedule_id INT AUTO_INCREMENT PRIMARY KEY,
--     provider_id INT,  -- Foreign key to service provider
--     available_date DATE,
--     set_time TIME,
--     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--     updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
--     FOREIGN KEY (provider_id) REFERENCES serviceproviders(provider_id)
-- );

CREATE TABLE provider_schedule (
    id INT AUTO_INCREMENT PRIMARY KEY,
    provider_id INT NOT NULL,
    available_date DATE NOT NULL,
    available_time TIME NOT NULL,
    status ENUM('available', 'booked') DEFAULT 'available',
    FOREIGN KEY (provider_id) REFERENCES serviceproviders(provider_id)
);



INSERT INTO serviceproviders (name, email, password, phone, citizenship_no, address, image, service_type, profile_description, latitude, longitude)
VALUES
-- Plumbing
('Anjana Shakya', 'anjana.shakya@gmail.com', '$2y$10$VzFHJQhtJ7bOsl8QJFGC1e', '9841123456', '123456789', 'Kathmandu, Nepal', 'ab.jpg', 'Plumbing', 'Expert in plumbing services for residential and commercial properties.', 27.717245, 85.323960),
('Ram Shah', 'ram.shah@gmail.com', '$2y$10$JlKjO0JHsb4QlsXKs4uIkO', '9841234567', '123456780', 'Lalitpur, Nepal', 'vision.jpg', 'Plumbing', 'Skilled plumber with over 10 years of experience.', 27.671000, 85.324000),
('Sita Gurung', 'sita.gurung@gmail.com', '$2y$10$H0kjN7KmZc9OqgBd0p2Ib.', '9801234567', '123456781', 'Bhaktapur, Nepal', 'ab.jpg', 'Plumbing', 'Providing reliable and affordable plumbing services.', 27.667000, 85.429000),

-- Electrician
('Kamal Thapa', 'kamal.thapa@gmail.com', '$2y$10$AsdQvIkZj7r5lbPf9qR9O.', '9801123456', '123456782', 'Kathmandu, Nepal', 'vision.jpg', 'Electrician', 'Certified electrician specializing in wiring and repairs.', 27.720000, 85.320000),
('Rita Sharma', 'rita.sharma@gmail.com', '$2y$10$EzHTKbDfdX3QwZXHc2tRce', '9841123444', '123456783', 'Lalitpur, Nepal', 'ab.jpg', 'Electrician', 'Experienced in electrical installations and troubleshooting.', 27.673000, 85.317000),
('Bishnu Maharjan', 'bishnu.maharjan@gmail.com', '$2y$10$C5HJN7yMZ7D9zQYmbPlj9e', '9841567890', '123456784', 'Bhaktapur, Nepal', 'vision.jpg', 'Electrician', 'Offering a wide range of electrical services.', 27.669000, 85.423000),

-- Carpentry
('Nabin Rai', 'nabin.rai@gmail.com', '$2y$10$VHTJnQLM2HLK7xZHIJN1y.', '9801567890', '123456785', 'Kathmandu, Nepal', 'ab.jpg', 'Carpentry', 'Expert carpenter for custom furniture and repairs.', 27.715000, 85.318000),
('Sangita Tamang', 'sangita.tamang@gmail.com', '$2y$10$VJHLmk8ZLKo1zNLyQHJK9m', '9801123444', '123456786', 'Lalitpur, Nepal', 'vision.jpg', 'Carpentry', 'Specialist in residential and commercial carpentry.', 27.675000, 85.325000),
('Raju Prajapati', 'raju.prajapati@gmail.com', '$2y$10$HJL6kZ3M7KJhlnJNY9kOlO', '9801987654', '123456787', 'Bhaktapur, Nepal', 'ab.jpg', 'Carpentry', 'Providing high-quality carpentry services.', 27.671000, 85.420000),

-- Painting
('Sunita Shrestha', 'sunita.shrestha@gmail.com', '$2y$10$JmNl8pLnL5kJmQkNY8MLp.', '9801345678', '123456788', 'Kathmandu, Nepal', 'vision.jpg', 'Painting', 'Professional painting services for homes and offices.', 27.719000, 85.316000),
('Rajendra Adhikari', 'rajendra.adhikari@gmail.com', '$2y$10$JLM7KoLh6JLlk8MlPQNRp.', '9841654321', '123456789', 'Lalitpur, Nepal', 'ab.jpg', 'Painting', 'Experienced painter specializing in interior and exterior painting.', 27.679000, 85.322000),
('Anju Manandhar', 'anju.manandhar@gmail.com', '$2y$10$HJLn8KLZLoMjJNjMLJNpY9', '9801765432', '123456790', 'Bhaktapur, Nepal', 'vision.jpg', 'Painting', 'Offering a wide range of painting services.', 27.667000, 85.424000),

-- Gardening
('Kiran Basnet', 'kiran.basnet@gmail.com', '$2y$10$LHl0nmN3nHJMmQPLKNlJ8e', '9841123555', '123456791', 'Kathmandu, Nepal', 'ab.jpg', 'Gardening', 'Providing expert gardening services for residential and commercial properties.', 27.718000, 85.317000),
('Mina Singh', 'mina.singh@gmail.com', '$2y$10$LJNkLmlhMLK7NmPLNkJOlk', '9801223344', '123456792', 'Lalitpur, Nepal', 'vision.jpg', 'Gardening', 'Specializing in garden design and maintenance.', 27.670000, 85.327000),
('Dilip Shrestha', 'dilip.shrestha@gmail.com', '$2y$10$HJL8KoLNJLKJnmLN9mlJ0l', '9801765432', '123456793', 'Bhaktapur, Nepal', 'ab.jpg', 'Gardening', 'Offering a wide range of gardening services.', 27.668000, 85.425000),

-- Interior Design
('Asha Thapa', 'asha.thapa@gmail.com', '$2y$10$KLmJ8pLmL5KJmQkNLJ8Mk9', '9841223344', '123456794', 'Kathmandu, Nepal', 'vision.jpg', 'Interior Design', 'Professional interior designer for residential and commercial spaces.', 27.716000, 85.319000),
('Prabin Rana', 'prabin.rana@gmail.com', '$2y$10$MJL8pLnmKL7NjNkLmQOPRl', '9801123344', '123456795', 'Lalitpur, Nepal', 'ab.jpg', 'Interior Design', 'Specialist in modern and contemporary interior design.', 27.678000, 85.321000),
('Rekha Khadka', 'rekha.khadka@gmail.com', '$2y$10$MJLK9kLnjLm7NmJLNKMP0r', '9801456789', '123456796', 'Bhaktapur, Nepal', 'vision.jpg', 'Interior Design', 'Offering creative interior design solutions.', 27.670000, 85.422000),

-- Cleaning
('Gita Maharjan', 'gita.maharjan@gmail.com', '$2y$10$NLM9koNmMLj7NjLNKOQPRp', '9801234560', '123456797', 'Kathmandu, Nepal', 'ab.jpg', 'Cleaning', 'Providing reliable and efficient cleaning services.', 27.717500, 85.315000),
('Hari Shrestha', 'hari.shrestha@gmail.com', '$2y$10$MLk7JNL8KoMPLkJ8MLJ0L9', '9801123333', '123456798', 'Lalitpur, Nepal', 'vision.jpg', 'Cleaning', 'Expert in residential and office cleaning.', 27.672000, 85.328000),
('Maya Shakya', 'maya.shakya@gmail.com', '$2y$10$HLJLm8KLMo7MnJKLNPQ0lN', '9841123344', '123456799', 'Bhaktapur, Nepal', 'ab.jpg', 'Cleaning', 'Offering affordable cleaning services.', 27.668500, 85.421000);





