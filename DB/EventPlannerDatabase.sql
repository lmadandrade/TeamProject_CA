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

-- DROP DATABASE event_planner;
CREATE DATABASE event_planner;
USE event_planner;

CREATE TABLE users (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL
);

-- events table
CREATE TABLE events (
  id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL,            -- event creator
  title VARCHAR(100) NOT NULL,
  description TEXT,
  event_date DATETIME NOT NULL,   
  event_end_date DATETIME, 
  location VARCHAR(255),
  social_link VARCHAR(255),
  CONSTRAINT fk_events_user
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- event participants table
CREATE TABLE event_participants (
  id INT PRIMARY KEY AUTO_INCREMENT,
  event_id INT NOT NULL,
  user_id INT,                          -- set if guest makes account
  email VARCHAR(255),                   -- for non users
  status VARCHAR(10) NOT NULL DEFAULT 'pending',     
  reminder_minutes_before INT NOT NULL DEFAULT 1440, -- default 24h
  reminder_sent BOOLEAN NOT NULL DEFAULT FALSE,      -- TRUE/FALSE
  color VARCHAR(7) DEFAULT '#3788d8',
  CONSTRAINT fk_ep_event FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
  CONSTRAINT fk_ep_user  FOREIGN KEY (user_id)  REFERENCES users(id) ON DELETE SET NULL
);
