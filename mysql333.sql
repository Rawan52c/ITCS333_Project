CREATE DATABASE IT_College_Room_Booking;
USE IT_College_Room_Booking;


CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

ALTER TABLE users 
ADD COLUMN reset_token VARCHAR(255) DEFAULT NULL,
ADD COLUMN passrecover_expires DATETIME DEFAULT NULL;



CREATE TABLE rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    capacity INT NOT NULL,
    equipment TEXT,
    description TEXT
);


CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    room_id INT,
    start_time DATETIME,
    end_time DATETIME,
    status ENUM('confirmed', 'cancelled') DEFAULT 'confirmed',
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (room_id) REFERENCES rooms(id)
);



INSERT INTO users (name, email, password, role) VALUES
('Mohamed Ali', 'mohamedali@uob.edu.bh', '$2y$10$E.atJJiPvS6TGpcLfBgQ..7gldKUl4A5z3wjNpl2yWZXgm7qQQ4jO', 'user'),
('Zain Yousef', 'zainyousef@uob.edu.bh', '$2y$10$E.atJJiPvS6TGpcLfBgQ..7gldKUl4A5z3wjNpl2yWZXgm7qQQ4jO', 'user'),
('Amina Adel', 'aminaadel@uob.edu.bh', '$2y$10$E.atJJiPvS6TGpcLfBgQ..7gldKUl4A5z3wjNpl2yWZXgm7qQQ4jO', 'admin'),
('Jassim Isa', 'jassimisa@uob.edu.bh', '$2y$10$E.atJJiPvS6TGpcLfBgQ..7gldKUl4A5z3wjNpl2yWZXgm7qQQ4jO', 'admin');



INSERT INTO rooms (name, capacity, equipment, description) VALUES
('Open Lab 2001', 80, 'Projector, Whiteboard, Computers, Tables, Chairs', 'A large room for meetings and presentations.'),
('Open Lab 1002', 200, 'Projector, Whiteboard, Computers, Tables, Chairs', 'A large room for seminars, lectures, team collaborations and brainstorming sessions.'),
('Class Room 28', 30, 'Whiteboard, Chairs, Projector', 'A class room for lectures and group discussions.'),
('Class Room 2084', 55, 'Whiteboard, Chairs, Projector', 'A class room for lectures and group discussions.'),
('Class Room 1089', 30, 'Whiteboard, Chairs, Projector, Computers, Network tools', 'A class room for lectures, network labs and group discussions.');



INSERT INTO reservations (user_id, room_id, start_time, end_time, status) VALUES
(1, 1, '2024-12-10 10:00:00', '2024-12-10 12:00:00', 'confirmed'),
(2, 2, '2024-12-11 14:00:00', '2024-12-11 16:00:00', 'confirmed'),
(1, 3, '2024-12-12 09:00:00', '2024-12-12 11:00:00', 'cancelled'),
(3, 4, '2024-12-15 13:00:00', '2024-12-15 14:00:00', 'confirmed');
