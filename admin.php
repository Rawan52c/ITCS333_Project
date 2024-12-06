<?php
require 'core.php';  // Include core functions

// Ensure the user is logged in and is an admin
requireLogin();  
if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php");  // Redirect if the user is not an admin
    exit;
}

// Handle room addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_room'])) {
    $name = $_POST['name'];
    $capacity = $_POST['capacity'];
    $equipment = $_POST['equipment'];
    $description = $_POST['description'];

    // Insert the new room into the database
    $success = executeQuery(
        "INSERT INTO rooms (name, capacity, equipment, description) VALUES (?, ?, ?, ?)", 
        [$name, $capacity, $equipment, $description]
    );

    if ($success) {
        $message = "Room added successfully!";
    } else {
        $error = "Failed to add the room. Please try again.";
    }
}

// Handle room deletion
if (isset($_GET['delete_room'])) {
    $roomId = $_GET['delete_room'];
    $success = executeQuery("DELETE FROM rooms WHERE id = ?", [$roomId]);
    if ($success) {
        $message = "Room deleted successfully!";
    } else {
        $error = "Failed to delete the room.";
    }
}

// Handle reservation deletion
if (isset($_GET['delete_reservation'])) {
    $reservationId = $_GET['delete_reservation'];
    $success = executeQuery("DELETE FROM reservations WHERE id = ?", [$reservationId]);
    if ($success) {
        $message = "Reservation deleted successfully!";
    } else {
        $error = "Failed to delete the reservation.";
    }
}

// Handle user deletion
if (isset($_GET['delete_user'])) {
    $userId = $_GET['delete_user'];
    $success = executeQuery("DELETE FROM users WHERE id = ?", [$userId]);
    if ($success) {
        $message = "User deleted successfully!";
    } else {
        $error = "Failed to delete the user.";
    }
}

// Fetch rooms, reservations, and users
$rooms = fetchAll("SELECT * FROM rooms");
$reservations = fetchAll("SELECT r.name AS room_name, u.name AS user_name, res.start_time, res.end_time, res.status, res.id AS reservation_id FROM reservations res JOIN rooms r ON res.room_id = r.id JOIN users u ON res.user_id = u.id");
$users = fetchAll("SELECT * FROM users");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/main.css" rel="stylesheet">
</head>
<body>

<?php include 'header.php'; ?>  <!-- Include the header here -->

<div class="container mt-5">
    <h2>Admin Panel</h2>

    <!-- Display messages -->
    <?php if (isset($message)): ?>
        <div class="alert alert-success" role="alert">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php elseif (isset($error)): ?>
        <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <!-- Add Room Form -->
    <h3>Add Room</h3>
    <form method="post" class="mt-4">
        <div class="row g-3">
            <div class="col-md-6">
                <input type="text" class="form-control" name="name" placeholder="Room Name" required>
            </div>
            <div class="col-md-4">
                <input type="number" class="form-control" name="capacity" placeholder="Capacity" required>
            </div>
            <div class="col-md-6">
                <textarea class="form-control" name="equipment" placeholder="Equipment"></textarea>
            </div>
            <div class="col-md-6">
                <textarea class="form-control" name="description" placeholder="Room Description"></textarea>
            </div>
            <div class="col-md-2">
                <button type="submit" name="add_room" class="btn btn-primary w-100">Add Room</button>
            </div>
        </div>
    </form>

    <!-- List of Rooms -->
    <h3 class="mt-5">Rooms</h3>
    <ul class="list-group mt-4">
        <?php foreach ($rooms as $room): ?>
            <li class="list-group-item d-flex justify-content-between">
                <?= htmlspecialchars($room['name']) ?> (Capacity: <?= $room['capacity'] ?>)
                <a href="?delete_room=<?= $room['id'] ?>" class="btn btn-danger btn-sm">Delete</a>
            </li>
        <?php endforeach; ?>
    </ul>

    <!-- List of Reservations -->
    <h3 class="mt-5">Reservations</h3>
    <ul class="list-group mt-4">
        <?php foreach ($reservations as $reservation): ?>
            <li class="list-group-item d-flex justify-content-between">
                Room: <?= htmlspecialchars($reservation['room_name']) ?>, User: <?= htmlspecialchars($reservation['user_name']) ?>, 
                From: <?= htmlspecialchars($reservation['start_time']) ?> to <?= htmlspecialchars($reservation['end_time']) ?>, 
                Status: <?= htmlspecialchars($reservation['status']) ?>
                <a href="?delete_reservation=<?= $reservation['reservation_id'] ?>" class="btn btn-danger btn-sm">Delete</a>
            </li>
        <?php endforeach; ?>
    </ul>

    <!-- List of Users -->
    <h3 class="mt-5">Users</h3>
    <ul class="list-group mt-4">
        <?php foreach ($users as $user): ?>
            <li class="list-group-item d-flex justify-content-between">
                <?= htmlspecialchars($user['name']) ?> (<?= htmlspecialchars($user['email']) ?>)
                <a href="?delete_user=<?= $user['id'] ?>" class="btn btn-danger btn-sm">Delete</a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<?php include 'footer.php'; ?>  <!-- Include footer here -->

</body>
</html>
