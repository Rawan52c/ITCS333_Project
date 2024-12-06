<?php
require 'core.php';

$search = $_GET['search'] ?? ''; //Get the search term, default to an empty string.

// Fetch rooms based on the search term
if(!empty($search)){
    $rooms = fetchAll("SELECT * FROM rooms WHERE name LIKE ?", ['%' . $search . '%']);
} else {
    $rooms = fetchAll("SELECT * FROM rooms");
}

// Fetch bookings for rooms
$roomAvailability = [];
foreach ($rooms as $room) {

    // Get all reservations for the current room
    $reservations = fetchAll(
        "SELECT * FROM reservations WHERE room_id = ? AND status = 'confirmed' ORDER BY start_time", 
        [$room['id']]
    );
    
    // Check if the room is available
    if (empty($reservations)) {
        $roomAvailability[$room['id']] = 'Available';  // Room is not booked
    } else {
        // Find the next available time slot
        $nextAvailable = null;
        $now = new DateTime();
        
        // Find the next available slot after the last booking
        foreach ($reservations as $reservation) {
            $endTime = new DateTime($reservation['end_time']);
            if ($endTime > $now) {
                $nextAvailable = $endTime->format('Y-m-d H:i');
                break; // We found the next available time
            }
        }

        // If there is no upcoming booking, the room is available
        if (!$nextAvailable) {
            $roomAvailability[$room['id']] = 'Available';
        } else {
            $roomAvailability[$room['id']] = "Next available: " . $nextAvailable;
        }
    }
}

// Handle no results
if (empty($rooms)) {
    $noResults = "No rooms found.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rooms</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/main.css" rel="stylesheet">
</head>
<body>

<?php include 'header.php'; ?>  
<?php include 'hero.php'; ?>

<div class="container mt-5">
    <h2>Room Browsing</h2>
    <form method="get" class="mt-4">
        <div class="mb-3">
            <label for="search" class="form-label">Search</label>
            <input type="text" class="form-control" name="search" id="search" value="<?= htmlspecialchars($search) ?>">
        </div>
        <button type="submit" class="btn btn-primary">Search</button>
    </form>

    <?php if (isset($noResults)): ?>
        <div class="alert alert-warning mt-4"><?= htmlspecialchars($noResults) ?></div>
    <?php else: ?>
        <table class="table table-striped mt-4">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Capacity</th>
                    <th>Equipment</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rooms as $index => $room): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($room['name']) ?></td>
                        <td><?= htmlspecialchars($room['capacity']) ?></td>
                        <td><?= htmlspecialchars($room['equipment'] ?? 'N/A') ?></td>
                        <td>
                            <?= htmlspecialchars($roomAvailability[$room['id']]) ?>
                        </td>
                        <td>
                            <?php if ($roomAvailability[$room['id']] == 'Available'): ?>
                                <a href="book_room.php?room_id=<?= htmlspecialchars($room['id']) ?>" class="btn btn-primary btn-sm">Book</a>
                            <?php else: ?>
                                <span class="badge bg-secondary">Not Available</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>  <!-- Include footer here -->

</body>
</html>
