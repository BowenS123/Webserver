<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welkom bij onze mij Applicatie</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            text-align: center;
        }
        header {
            background-color: #333;
            color: #fff;
            padding: 10px 0;
        }
        h1 {
            margin-top: 0;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        form {
            margin-top: 20px;
        }
        form input[type="text"],
        form input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        form input[type="submit"] {
            background-color: #333;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <header>
        <h1>AdminPage</h1>
    </header>
    <div class="container">
<?php
session_start(); // Start de sessie

// Databaseverbinding opzetten
$dbconn = pg_connect("host=localhost dbname=postgres user=postgres password=password");

// Functie voor het verwerken van een DELETE-verzoek
function handleDeleteRequest($dbconn) {
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
        // Controleer of de gebruiker is ingelogd
        if (!isLoggedIn()) {
            echo "U moet eerst inloggen om deze actie uit te voeren.";
            return;
        }

        $deleteId = $_POST['delete_id'];
        $query = "DELETE FROM contacten WHERE id = $1";
        $result = pg_query_params($dbconn, $query, array($deleteId));
        if ($result) {
            echo "Bericht met ID $deleteId is succesvol verwijderd.";
        } else {
            echo "Er is een fout opgetreden bij het verwijderen van het bericht met ID $deleteId.";
        }
    }
}

// Functie voor het verwerken van een inlogverzoek
function handleLoginRequest($dbconn) {
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["username"]) && isset($_POST["password"])) {
        // Controleer of de inloggegevens overeenkomen met de admin
        $admin_username = "admin";
        $admin_password = "admin123";
        if ($_POST["username"] === $admin_username && $_POST["password"] === $admin_password) {
            $_SESSION['loggedIn'] = true; // Stel een sessievariabele in om aan te geven dat de gebruiker is ingelogd
            return true;
        } else {
            echo "Foutieve inloggegevens. Probeer opnieuw.";
        }
    }
    return false;
}

// Functie om te controleren of de gebruiker is ingelogd
function isLoggedIn() {
    return isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true;
}

// Functie voor uitloggen
function handleLogout() {
    if (isset($_POST["logout"])) {
        session_unset();
        session_destroy(); 
        echo "U bent uitgelogd.";
    }
}

// Controleren of er een DELETE-verzoek is ontvangen
handleDeleteRequest($dbconn);

// Controleren of er een uitlogverzoek is ontvangen
handleLogout();

// Controleren of er een inlogverzoek is ontvangen
$loggedIn = handleLoginRequest($dbconn);

// Als de gebruiker is ingelogd, toon de beheerpagina
if ($loggedIn || isLoggedIn()) {
    // Query om alle contacten op te halen
    $query = "SELECT * FROM contacten";
    $result = pg_query($dbconn, $query);

    // Controleren of er resultaten zijn
    if (!$result) {
        echo "Er zijn geen contacten gevonden.";
    } else {
        // Contacten weergeven
        echo "<h2>Contacten</h2>";
        echo "<ul>";
        while ($row = pg_fetch_assoc($result)) {
            echo "<li><strong>Naam:</strong> " . $row['naam'] . " - <strong>Email:</strong> " . $row['email'] . " - <strong>Beschrijving:</strong> " . $row['beschrijving'] . "</li>";
            echo "<form method='post'>";
            echo "<input type='hidden' name='delete_id' value='" . $row['id'] . "'>";
            echo "<input type='submit' value='Verwijderen'>";
            echo "</form>";
            echo "</li>";
        }
        echo "</ul>";
    }
} else {
    // Toon het inlogformulier
?>
    <h2>Log in als beheerder</h2>
    <form method="post">
        <label for="username">Gebruikersnaam:</label>
        <input type="text" id="username" name="username" required><br>

        <label for="password">Wachtwoord:</label>
        <input type="password" id="password" name="password" required><br>

        <input type="submit" value="Inloggen">
    </form>
<?php
}
// uitloggen
if (isLoggedIn()) {
?>
    <form method="post">
        <input type="submit" name="logout" value="Uitloggen">
    </form>
<?php
}
// Databaseverbinding sluiten
pg_close($dbconn);
?>
    </div>
</body>
</html>
