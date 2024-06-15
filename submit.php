<?php
// Database connection parameters
$dbhost = 'localhost';
$dbname = 'postgres'; 
$dbuser = 'postgres';
$dbpass = 'password';

// Connect to the database
$conn = pg_connect("host=$dbhost dbname=$dbname user=$dbuser password=$dbpass");

// Check if the connection was successful
if (!$conn) {
    echo json_encode(['error' => 'Unable to connect to the database']);
    exit;
}

// Get the form data
$name = $_POST['name'];
$email = $_POST['email'];
$message = $_POST['message'];

// Insert the form data into the database
$query = "INSERT INTO contacten (naam, email, beschrijving) VALUES ('$name', '$email', '$message')";
$result = pg_query($conn, $query);

// Check if the query was successful
if ($result) {
    echo json_encode(['success' => 'Thank you! Your message has been sent successfully.']);
} else {
    echo json_encode(['error' => 'Unable to submit your message. Please try again later.']);
}

// Close the database connection
pg_close($conn);
?>
