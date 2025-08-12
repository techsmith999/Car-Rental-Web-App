-- Create the `car_rental` database
CREATE DATABASE IF NOT EXISTS `car_rental`;
USE `car_rental`;

-- Create the `admins` table
CREATE TABLE IF NOT EXISTS `admins` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `username` VARCHAR(100) NOT NULL UNIQUE, -- Ensure the username is unique
  `email` VARCHAR(255) NOT NULL UNIQUE,    -- Ensure the email is unique
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create the `cars` table
CREATE TABLE IF NOT EXISTS `cars` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `make` VARCHAR(255) NOT NULL,
  `model` VARCHAR(255) NOT NULL,
  `year` INT(4) NOT NULL CHECK (`year` >= 1886), -- Ensure the year is realistic
  `color` VARCHAR(50) NOT NULL,
  `price_per_day` DECIMAL(10,2) NOT NULL CHECK (`price_per_day` > 0), -- Ensure price is positive
  `availability_status` ENUM('available', 'rented', 'maintenance') NOT NULL DEFAULT 'available',
  `mileage` INT(11) NOT NULL,
  `fuel_type` ENUM('petrol', 'diesel', 'electric', 'hybrid') NOT NULL,
  `car_type` ENUM('sedan', 'suv', 'coupe', 'hatchback', 'convertible', 'minivan', 'truck') NOT NULL,
  `image` LONGBLOB DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  `description` TEXT DEFAULT NULL,
  `location` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create the `users` table
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,  -- Ensure the email is unique
  `phone` VARCHAR(20) DEFAULT NULL,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create the `bookings` table
CREATE TABLE IF NOT EXISTS `bookings` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `car_id` INT(11) NOT NULL,
  `pickup_date` DATETIME NOT NULL,
  `return_date` DATETIME NOT NULL,
  `pickup_status` ENUM('pending', 'picked_up', 'returned') NOT NULL DEFAULT 'pending',
  `return_status` ENUM('pending', 'returned') NOT NULL DEFAULT 'pending',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE, -- Add on delete cascade to maintain integrity
  FOREIGN KEY (`car_id`) REFERENCES `cars`(`id`) ON DELETE CASCADE -- Add on delete cascade to maintain integrity
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
