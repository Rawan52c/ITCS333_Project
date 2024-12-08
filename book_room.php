<?php
require 'core.php';  // Include the core functions

requireLogin();  // Ensure the user is logged in

$userId = $_SESSION['user_id'];  // Get the logged-in user's ID

if (isset($_GET['room_id'])) {
    $roomId = $_GET['room_id'];

    // Fetch room details
    $room = fetchOne("SELECT * FROM rooms WHERE id = ?", [$roomId]);

    if (!$room) {
        die("Room not found.");
    }

    // Handle form submission for booking
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $startTime = $_POST['start_time'];
        $endTime = $_POST['end_time'];

        // Validate that start time is before end time
        if (strtotime($startTime) >= strtotime($endTime)) {
            $error = "End time must be after the start time.";
        } else {
            // Check for conflicting bookings
            $conflict = fetchOne(
                "SELECT * FROM reservations WHERE room_id = ? AND 
                 ((start_time <= ? AND end_time > ?) OR 
                  (start_time < ? AND end_time >= ?))",
                [$roomId, $endTime, $startTime, $endTime, $startTime]
            );

            if ($conflict) {
                $error = "This room is already booked during the selected time.";
            } else {
                // Insert new booking into the database
                $success = executeQuery(
                    "INSERT INTO reservations (user_id, room_id, start_time, end_time, status) VALUES (?, ?, ?, ?, 'confirmed')",
                    [$userId, $roomId, $startTime, $endTime]
                );

                if ($success) {
                    $message = "Room successfully booked!";
                } else {
                    $error = "Failed to book the room. Please try again.";
                }
            }
        }
    }
} else {
    die("Room ID is missing.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Room</title>
    <link rel="stylesheet" href="https://unpkg.com/@picocss/pico@1.5.10/css/pico.min.css"> <!-- Pico.css -->
</head>
<body>

<?php include 'header.php'; ?>  <!-- Include the header here -->

<div class="container">
    <h2>Book <?= htmlspecialchars($room['name']) ?></h2>

    <!-- Display error or success messages -->
    <?php if (isset($error)): ?>
        <article class="alert error">
            <?= htmlspecialchars($error) ?>
        </article>
    <?php elseif (isset($message)): ?>
        <article class="alert success">
            <?= htmlspecialchars($message) ?>
        </article>
    <?php endif; ?>

    <form method="post" class="mt-4">
        <label for="start_time">Start Time</label>
        <input type="datetime-local" name="start_time" id="start_time" required>

        <label for="end_time">End Time</label>
        <input type="datetime-local" name="end_time" id="end_time" required>

        <button type="submit" class="button">Book Room</button>
    </form>
</div>

<script>
    // Add a confirmation prompt before form submission
    document.querySelector('form').addEventListener('submit', function (e) {
        const startTime = new Date(document.getElementById('start_time').value);
        const endTime = new Date(document.getElementById('end_time').value);

        if (startTime >= endTime) {
            e.preventDefault();
            alert('End time must be after the start time.');
        }
    });
</script>

<?php include 'footer.php'; ?>  <!-- Include footer here -->

</body>
</html>
