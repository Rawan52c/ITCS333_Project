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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/main.css" rel="stylesheet">
</head>
<body>

<?php include 'header.php'; ?>  <!-- Include the header here -->

<!-- Hero Section -->
<?php include 'hero.php'; ?>  <!-- Include the hero section -->

<div class="container mt-5">
    <h2>Book <?= htmlspecialchars($room['name']) ?></h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php elseif (isset($message)): ?>
        <div class="alert alert-success" role="alert">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form method="post" class="mt-4">
        <div class="mb-3">
            <label for="start_time" class="form-label">Start Time</label>
            <input type="datetime-local" class="form-control" name="start_time" id="start_time" required>
        </div>
        <div class="mb-3">
            <label for="end_time" class="form-label">End Time</label>
            <input type="datetime-local" class="form-control" name="end_time" id="end_time" required>
        </div>
        <button type="submit" class="btn btn-primary">Book Room</button>
    </form>
</div>

<?php include 'footer.php'; ?>  <!-- Include footer here -->

</body>
</html>
