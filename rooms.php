<?php
require 'core.php';

$search = $_GET['search'] ?? ''; //Get the search term, default to an empty string.

// Fetch rooms based on the search term
if (!empty($search)) {
    $rooms = fetchAll("SELECT * FROM rooms WHERE name LIKE ?", ['%' . $search . '%']);
} else {
    $rooms = fetchAll("SELECT * FROM rooms");
}

// Fetch bookings for rooms
$roomAvailability = [];
foreach ($rooms as $room) {
    $reservations = fetchAll(
        "SELECT * FROM reservations WHERE room_id = ? AND status = 'confirmed' ORDER BY start_time",
        [$room['room_id']]
    );

    $now = new DateTime();
    $isAvailable = true;
    $nextAvailable = null;

    foreach ($reservations as $reservation) {
        $startTime = new DateTime($reservation['start_time']);
        $endTime = new DateTime($reservation['end_time']);

        if ($now >= $startTime && $now <= $endTime){
            $isAvailable = false;
            $nextAvailable = $endTime->format('Y-m-d H:i');
            break;
        }

        if($startTime > $now && ($nextAvailable === null || $startTime < new DateTime($nextAvailable))){
            $nextAvailable = $startTime->format('Y-m-d H:i');
        }
    }

    if ($isAvailable) {
        $roomAvailability[$room['room_id']] = 'Available';
    } else {
        $roomAvailability[$room['room_id']] = 'Next Available' . $nextAvailable;
    }
}


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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/picocss/pico.min.css">
    <link rel="stylesheet" href="styles.css">
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('search');
            const searchForm = document.querySelector('form');
            let timeout;
            searchInput.addEventListener('input', () => {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                searchForm.submit();}, 2500);
            });
        });
    </script>
</head>
<body>

    <?php include 'header.php'; ?>  

    <main class="container">
        
        <h2>Room Browsing</h2>
        <form method="get" class="grid">
            <label for="search">
                Search
                <input type="text" name="search" id="search" value="<?= htmlspecialchars($search) ?>" placeholder="Enter room name">
            </label>
            <button type="submit" role="button" class="primary">Search</button>
        </form>

        <?php if (isset($noResults)): ?>
            <article class="alert warning"><?= htmlspecialchars($noResults) ?></article>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Capacity</th>
                        <th>Equipment</th>
                        <th>Status</th>
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
                                <?php if ($roomAvailability[$room['room_id']] == 'Available'): ?>
                                    <a href="book_room.php?room_id=<?= htmlspecialchars($room['room_id']) ?>" role="button">Book</a>
                                <?php elseif (is_array($roomAvailability[$room['room_id']])): ?>
                                    <span><?= htmlspecialchars($roomAvailability[$room['room_id']]['status']) ?> (Next available: <?= htmlspecialchars($roomAvailability[$room['room_id']]['next_time']) ?>)</span>
                                    <a href="book_room.php?room_id=<?= htmlspecialchars($room['room_id']) ?>&next_time=<?= urlencode($roomAvailability[$room['room_id']]['next_time']) ?>" role="button">Book Next Available Time</a>
                                <?php else: ?>    
                                    <span class="badge secondary">Not Available</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>

    <?php include 'footer.php'; ?>  

</body>
</html>
