<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$user_position = $_SESSION['user_position'];
?>
<?php
require 'core.php';  // Include the core functions

requireLogin();  // Ensure the user is logged in

$userId = $_SESSION['user_id'];  // Get the logged-in user's ID

// Handle room deletion
if (isset($_GET['delete'])) {
    $reservationId = $_GET['delete'];

    // Check if the reservation belongs to the logged-in user
    $reservation = fetchOne("SELECT * FROM reservations WHERE id = ? AND user_id = ?", [$reservationId, $userId]);

    if ($reservation) {
        // Delete the reservation
        $success = executeQuery("DELETE FROM reservations WHERE id = ?", [$reservationId]);

        if ($success) {
            $message = "Room reservation deleted successfully!";
        } else {
            $error = "Failed to delete the reservation. Please try again.";
        }
    } else {
        $error = "You are not authorized to delete this reservation.";
    }
}

// Fetch the user's reservations from the database
$bookedRooms = fetchAll(
    "SELECT r.name AS room_name, r.capacity, r.equipment, res.start_time, res.end_time, res.status, res.id AS reservation_id
     FROM reservations res
     JOIN rooms r ON res.room_id = r.id
     WHERE res.user_id = ?",
    [$userId]
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to UOB IT College Room Booking</title>
    <link rel="stylesheet" href="https://unpkg.com/@picocss/pico@1.5.10/css/pico.min.css">
</head>
<body>
    <main class="container">
        <header>
            <hgroup>
                <h1>Welcome, <?= htmlspecialchars($username) ?>!</h1>
                <p>Your role: <?= htmlspecialchars($user_position) ?></p>
            </hgroup>
            <nav>
                <a href="logout.php" role="button">Logout</a>
            </nav>
        </header>

        <?php include 'header.php'; ?>  <!-- Include the header here -->

        <!-- Hero Section -->
        <?php include 'hero.php'; ?>  <!-- Include the hero section -->

        <section>
            <h2>Your Booked Rooms</h2>

            <?php if (isset($error)): ?>
                <article class="alert error">
                    <?= htmlspecialchars($error) ?>
                    <button onclick="this.parentElement.remove();">&times;</button>
                </article>
            <?php elseif (isset($message)): ?>
                <article class="alert success">
                    <?= htmlspecialchars($message) ?>
                    <button onclick="this.parentElement.remove();">&times;</button>
                </article>
            <?php endif; ?>

            <?php if (empty($bookedRooms)): ?>
                <article class="alert info">
                    You have not booked any rooms yet.
                </article>
            <?php else: ?>
                <table role="grid">
                    <thead>
                        <tr>
                            <th>Room Name</th>
                            <th>Capacity</th>
                            <th>Equipment</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookedRooms as $room): ?>
                            <tr>
                                <td><?= htmlspecialchars($room['room_name']) ?></td>
                                <td><?= htmlspecialchars($room['capacity']) ?></td>
                                <td><?= htmlspecialchars($room['equipment']) ?></td>
                                <td><?= htmlspecialchars(date('Y-m-d H:i', strtotime($room['start_time']))) ?></td>
                                <td><?= htmlspecialchars(date('Y-m-d H:i', strtotime($room['end_time']))) ?></td>
                                <td>
                                    <span class="<?= $room['status'] === 'confirmed' ? 'success' : 'danger' ?>">
                                        <?= htmlspecialchars(ucfirst($room['status'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="index.php?delete=<?= htmlspecialchars($room['reservation_id']) ?>" 
                                       role="button" 
                                       class="contrast"
                                       onclick="return confirm('Are you sure you want to delete this reservation?');">
                                       Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>

        <?php include 'footer.php'; ?>  <!-- Include footer here -->
    </main>
</body>
</html>
