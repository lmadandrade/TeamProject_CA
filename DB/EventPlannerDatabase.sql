-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 10, 2025 at 09:46 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

 -- DROP DATABASE event_planner;
-- SELECT NOW();
select * from events;

CREATE DATABASE event_planner;
USE event_planner;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Create `users` table
CREATE TABLE `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `password` VARCHAR(225) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create events table
CREATE TABLE `events` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `title` VARCHAR(100) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `event_date` DATETIME NOT NULL,
  `visibility` ENUM('private','group') DEFAULT 'private',
  `reminder_time` INT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `event_end_date` DATETIME DEFAULT NULL,
  `event_time` TIME DEFAULT NULL,
  `location` VARCHAR(255) DEFAULT NULL,
  `participants` TEXT DEFAULT NULL,
  `social_link` VARCHAR(255) DEFAULT NULL,
  `color` VARCHAR(20) DEFAULT '#3498db',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `events_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create `invitations` table
CREATE TABLE `invitations` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `event_id` INT(11) NOT NULL,
  `sender_id` INT(11) NOT NULL,
  `recipient_email` VARCHAR(100) NOT NULL,
  `status` ENUM('pending','accepted','declined') DEFAULT 'pending',
  `sent_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `event_id` (`event_id`),
  KEY `sender_id` (`sender_id`),
  CONSTRAINT `invitations_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invitations_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create `event_responses` table
CREATE TABLE `event_responses` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `invitation_id` INT(11) NOT NULL,
  `responder_id` INT(11) DEFAULT NULL,
  `response` ENUM('accepted','declined') NOT NULL,
  `responded_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `invitation_id` (`invitation_id`),
  KEY `responder_id` (`responder_id`),
  CONSTRAINT `event_responses_ibfk_1` FOREIGN KEY (`invitation_id`) REFERENCES `invitations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_responses_ibfk_2` FOREIGN KEY (`responder_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;