<?php
// Establish database connection
$dbconn = pg_connect("host=localhost dbname=postgres user=postgres password=password");

// Check connection
if (!$dbconn) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Query to fetch message count per day
$query = "SELECT DATE(created_at) AS message_date, COUNT(*) AS message_count
          FROM contacten
          GROUP BY DATE(created_at)
          ORDER BY message_date";

$result = pg_query($dbconn, $query);

if (!$result) {
    echo json_encode(['error' => 'Failed to fetch data']);
    exit;
}

// Fetch data and store in an associative array
$data = [];
while ($row = pg_fetch_assoc($result)) {
    $data[] = [
        'message_date' => $row['message_date'],
        'message_count' => intval($row['message_count']) // Ensure message_count is an integer
    ];
}

// Close database connection
pg_close($dbconn);

// Return data as JSON
echo json_encode($data);

?>
