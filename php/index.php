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
    </style>
</head>
<body>
    <header>
        <h1>Welkom bij mij Web Applicatie</h1>
    </header>
    <div class="container">
        <p>Deze webapplicatie stelt ons in staat om...</p>

        <?php
        // Databaseverbinding opzetten
        $dbconn = pg_connect("host=localhost dbname=postgres user=postgres password=password");

        // Functie voor het verwerken van een DELETE-verzoek
        function handleDeleteRequest($dbconn) {
            if (isset($_POST['delete_id'])) {
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

        // Controleren of er een DELETE-verzoek is ontvangen
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            handleDeleteRequest($dbconn);
        }

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
                echo "<li><strong>Naam:</strong> " . $row['naam'] . " - <strong>Email:</strong> " . $row['email'] . " - <strong>Beschrijving:</strong> " . $row['beschrijving'];
                echo "<form method='post'>";
                echo "<input type='hidden' name='delete_id' value='" . $row['id'] . "'>";
                echo "<input type='submit' value='Verwijderen'>";
                echo "</form></li>";
            }
            echo "</ul>";
        }

        // Databaseverbinding sluiten
        pg_close($dbconn);
        ?>

    </div>
</body>
</html>

