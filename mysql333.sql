-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 01, 2024 at 08:19 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE DATABASE IT_College_Room_Booking;
USE IT_College_Room_Booking;

-- Database: `it_college_room_booking`

-- --------------------------------------------------------

-- Table structure for table `reservations`

CREATE TABLE `reservations` (
  `reservation_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `room_id` int(11) DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `status` enum('confirmed','cancelled') DEFAULT 'confirmed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Dumping data for table `reservations`

INSERT INTO `reservations` (`reservation_id`, `user_id`, `room_id`, `start_time`, `end_time`, `status`) VALUES
(1, 1, 1, '2024-12-10 10:00:00', '2024-12-10 12:00:00', 'confirmed'),
(2, 2, 2, '2024-12-11 14:00:00', '2024-12-11 16:00:00', 'confirmed'),
(3, 1, 3, '2024-12-12 09:00:00', '2024-12-12 11:00:00', 'cancelled'),
(4, 3, 4, '2024-12-15 13:00:00', '2024-12-15 14:00:00', 'confirmed'),

-- --------------------------------------------------------

-- Table structure for table `rooms`


CREATE TABLE `rooms` (
  `room_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `capacity` int(11) NOT NULL,
  `equipment` text DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `rooms`

INSERT INTO `rooms` (`room_id`, `name`, `capacity`, `equipment`, `description`) VALUES
(1, 'Open Lab 2001', 80, 'Projector, Whiteboard, Computers, Tables, Chairs', 'A large room for meetings and presentations.'),
(2, 'Open Lab 1002', 200, 'Projector, Whiteboard, Computers, Tables, Chairs', 'A large room for seminars, lectures, team collaborations and brainstorming sessions.'),
(3, 'Class Room 28', 30, 'Whiteboard, Chairs, Projector', 'A class room for lectures and group discussions.'),
(4, 'Class Room 2084', 55, 'Whiteboard, Chairs, Projector', 'A class room for lectures and group discussions.'),
(5, 'Class Room 1089', 30, 'Whiteboard, Chairs, Projector, Computers, Network tools', 'A class room for lectures, network labs and group discussions.');

-- --------------------------------------------------------
-- Table structure for table `users`

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `reset_token` varchar(255) DEFAULT NULL,
  `passrecover_expires` datetime DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT 'uploads/default.jpeg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `users`

INSERT INTO `users` (`id`, `user_name`, `email`, `password`, `role`, `updated_at`, `reset_token`, `passrecover_expires`, `profile_image`) VALUES
(1, 'Mohamed Ali', 'mohamedali@uob.edu.bh', '$2y$10$n07qvB6cSolncOs65ZFGg.jAMpr/jtyFaq3WZIvTR5VLnrj98qjgu', 'user', '2024-12-09 11:37:53', NULL, NULL, 'uploads/default.jpeg'),
(2, 'Zain Yousef', 'zainyousef@uob.edu.bh', '$2y$10$n07qvB6cSolncOs65ZFGg.jAMpr/jtyFaq3WZIvTR5VLnrj98qjgu', 'user', '2024-12-09 11:38:05', NULL, NULL, 'uploads/default.jpeg'),
(3, 'Amina Adel', 'aminaadel@uob.edu.bh', '$2y$10$n07qvB6cSolncOs65ZFGg.jAMpr/jtyFaq3WZIvTR5VLnrj98qjgu', 'admin', '2024-12-09 11:38:16', NULL, NULL, 'uploads/default.jpeg'),
(4, 'Jassim Isa', 'jassimisa@uob.edu.bh', '$2y$10$n07qvB6cSolncOs65ZFGg.jAMpr/jtyFaq3WZIvTR5VLnrj98qjgu', 'admin', '2024-12-09 11:38:26', NULL, NULL, 'uploads/default.jpeg'),
(5, 'alice', 'alice@uob.edu.bh', '$2y$10$n07qvB6cSolncOs65ZFGg.jAMpr/jtyFaq3WZIvTR5VLnrj98qjgu', 'user', '2024-12-09 12:36:45', NULL, NULL, 'uploads/default.jpeg'),
(6, 'bob', 'bob@uob.edu.bh', '$2y$10$IUpJXURnOVSXoAfsryAksOEqtSzdTY7wFODrYHTmnWtizylEfUzRW', 'user', '2024-12-09 16:50:10', NULL, NULL, 'uploads/default.jpeg'),
(7, 'Maria', 'maria@uob.edu.bh', '$2y$10$k/l.Zq2wxmmHzgs9TFwZ2.Y8VEPaDSKV/SPoor4vrmXA6b/vy53fy', 'user', '2024-12-09 18:46:53', NULL, NULL, 'uploads/default.jpeg');


-- Indexes for table `reservations`

ALTER TABLE `reservations`
  ADD PRIMARY KEY (`reservation_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `room_id` (`room_id`);


-- Indexes for table `rooms`

ALTER TABLE `rooms`
  ADD PRIMARY KEY (`room_id`);


-- Indexes for table `users`

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);



-- AUTO_INCREMENT for table `reservations`

ALTER TABLE `reservations`
  MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;


-- AUTO_INCREMENT for table `rooms`

ALTER TABLE `rooms`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;


-- AUTO_INCREMENT for table `users`

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;



-- Constraints for table `reservations`

ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`);
COMMIT;
