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

    if (empty($reservations)) {
        $roomAvailability[$room['room_id']] = 'Available';
    } else {
        $nextAvailable = null;
        $now = new DateTime();

        foreach ($reservations as $reservation) {
            $endTime = new DateTime($reservation['end_time']);
            if ($endTime > $now) {
                $nextAvailable = $endTime->format('Y-m-d H:i');
                break;
            }
        }

        if (!$nextAvailable) {
            $roomAvailability[$room['room_id']] = 'Available';
        } else {
            $roomAvailability[$room['room_id']] = "Next available: " . $nextAvailable;
        }
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
<?php include 'hero.php'; ?>

<main class="container">
    <h2>Room Browsing</h2>
    <form method="get" class="grid">
        <label for="search">
            Search
            <input type="text" name="search" id="search" value="<?= htmlspecialchars($search) ?>" placeholder="Enter room name">
        </label>
        <button type="submit" role="button">Search</button>
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
                        <td><?= htmlspecialchars($roomAvailability[$room['room_id']]) ?></td>
                        <td>
                            <?php if ($roomAvailability[$room['room_id']] == 'Available'): ?>
                                <a href="book_room.php?room_id=<?= htmlspecialchars($room['room_id']) ?>" role="button">Book</a>
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
