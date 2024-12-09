<?php
require 'core.php';  // Include core functions

// Ensure the user is logged in and is an admin
requireLogin();

$role = $_SESSION['role']; // Get the logged-in user's role

if ($role != 'admin') {
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
    $success = executeQuery("DELETE FROM rooms WHERE room_id = ?", [$roomId]);
    if ($success) {
        $message = "Room deleted successfully!";
    } else {
        $error = "Failed to delete the room.";
    }
}

// Handle reservation deletion
if (isset($_GET['delete_reservation'])) {
    $reservationId = $_GET['delete_reservation'];
    $success = executeQuery("DELETE FROM reservations WHERE reservation_id = ?", [$reservationId]);
    if ($success) {
        $message = "Reservation deleted successfully!";
    } else {
        $error = "Failed to delete the reservation.";
    }
}

// Handle user deletion
if (isset($_GET['delete_user'])) {
    $userId = $_GET['delete_user'];
    $success = executeQuery("DELETE FROM users WHERE user_id = ?", [$userId]);
    if ($success) {
        $message = "User deleted successfully!";
    } else {
        $error = "Failed to delete the user.";
    }
}

// Fetch rooms, reservations, and users
$rooms = fetchAll("SELECT * FROM rooms");
$reservations = fetchAll("SELECT r.name AS room_name, u.user_name AS user_name, res.start_time, res.end_time, res.status, res.reservation_id AS reservation_id FROM reservations res JOIN rooms r ON res.room_id = r.room_id JOIN users u ON res.user_id = u.id");
$users = fetchAll("SELECT * FROM users");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="https://unpkg.com/@picocss/pico@1.5.10/css/pico.min.css"> <!-- Pico.css CDN -->
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<?php include 'header.php'; ?> <!-- Include the header -->

<main class="container">
    <h1>Admin Panel</h1>

    <!-- Display messages -->
    <?php if (isset($message)): ?>
        <article class="alert success">
            <?= htmlspecialchars($message) ?>
        </article>
    <?php elseif (isset($error)): ?>
        <article class="alert error">
            <?= htmlspecialchars($error) ?>
        </article>
    <?php endif; ?>

    <!-- Add Room Form -->
    <section>
        <h2>Add Room</h2>
        <form method="post">
            <label for="name">Room Name</label>
            <input type="text" id="name" name="name" placeholder="Enter room name" required>

            <label for="capacity">Capacity</label>
            <input type="number" id="capacity" name="capacity" placeholder="Enter capacity" required>

            <label for="equipment">Equipment</label>
            <textarea id="equipment" name="equipment" placeholder="Enter equipment details"></textarea>

            <label for="description">Description</label>
            <textarea id="description" name="description" placeholder="Enter room description"></textarea>

            <button type="submit" name="add_room">Add Room</button>
        </form>
    </section>

    <!-- List of Rooms -->
    <section>
        <h2>Rooms</h2>
        <ul>
            <?php foreach ($rooms as $room): ?>
                <li>
                    <?= htmlspecialchars($room['name']) ?> (Capacity: <?= $room['capacity'] ?>)
                    <button class="delete-room" data-id="<?= $room['room_id'] ?>">Delete</button>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>

    <!-- List of Reservations -->
    <section>
        <h2>Reservations</h2>
        <ul>
            <?php foreach ($reservations as $reservation): ?>
                <li>
                    Room: <?= htmlspecialchars($reservation['room_name']) ?>, User: <?= htmlspecialchars($reservation['user_name']) ?>, 
                    From: <?= htmlspecialchars($reservation['start_time']) ?> to <?= htmlspecialchars($reservation['end_time']) ?>, 
                    Status: <?= htmlspecialchars($reservation['status']) ?>
                    <button class="delete-reservation" data-id="<?= $reservation['reservation_id'] ?>">Delete</button>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>

    <!-- List of Users -->
    <section>
        <h2>Users</h2>
        <ul>
            <?php foreach ($users as $user): ?>
                <li>
                    <?= htmlspecialchars($user['user_name']) ?> (<?= htmlspecialchars($user['email']) ?>)
                    <button class="delete-user" data-id="<?= $user['id'] ?>">Delete</button>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
</main>

<?php include 'footer.php'; ?> <!-- Include the footer -->

<script>
    // Add event listeners for delete buttons
    document.addEventListener('click', function (event) {
        if (event.target.matches('.delete-room')) {
            const roomId = event.target.dataset.id;
            if (confirm('Are you sure you want to delete this room?')) {
                window.location.href = `?delete_room=${roomId}`;
            }
        }

        if (event.target.matches('.delete-reservation')) {
            const reservationId = event.target.dataset.id;
            if (confirm('Are you sure you want to delete this reservation?')) {
                window.location.href = `?delete_reservation=${reservationId}`;
            }
        }

        if (event.target.matches('.delete-user')) {
            const userId = event.target.dataset.id;
            if (confirm('Are you sure you want to delete this user?')) {
                window.location.href = `?delete_user=${userId}`;
            }
        }
    });
</script>

</body>
</html>
