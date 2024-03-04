<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1 id="gastenboek">Gastenboek Invulpagina</h1>
    </header>
    <div class="navigationbar">
        <ul>
            <li class="navigation-item"><a href="index.html">Home</a></li>
            <li class="navigation-item"><a href="form.html">Form</a></li>
            <li class="navigation-item"><a href="contact.asp">Contact</a></li>
            <li class="navigation-item"><a href="about.asp">About</a></li>
        </ul>
    </div>

    <div class="reviews-container">
        <div class="review">
            <h3>Review 1</h3>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
        </div>
        
        <div class="review">
            <h3>Review 2</h3>
            <p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
        </div>
        
        <div class="review">
            <h3>Review 3</h3>
            <p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>
        </div>
        </div>
       
    <div class="form" >
    <?php
    // Controleer of het formulier al is ingediend in deze sessie
    if (!isset($_SESSION['form_submitted'])) {
    ?>
    <!-- Formulier wordt alleen weergegeven als het nog niet is ingediend -->
    <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post" enctype="multipart/form-data">
        Naam: <input class="feedback-input"  type="text" name="naam" required><br>
        Date: <input class="feedback-input" type="date" name="date" required><br>
        Korte tekst: <textarea class="feedback-input" name="korte_text" required></textarea><br>
        Afbeelding: <input type="file" name="afbeelding" accept="image/*" required><br>
        <input id="submit_button" type="submit" name="submit" value="Verzend formulier">
    </form>
    <?php
    }
    ?>   
    </div>
   
   <?php
   


// Verbinding maken met de database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gebruikers";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}

// Controleer of het formulier al is ingediend in deze sessie
if (!isset($_SESSION['form_submitted'])) {
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
        $naam = $_POST["naam"];
        $date = $_POST["date"];
        $korte_text = $_POST["korte_text"];
        $afbeelding = $_FILES["afbeelding"]["name"];
        $sessie_id = session_id();

        // Controleer of de sessie-ID al bestaat in de database
        $checkQuery = "SELECT COUNT(*) as count FROM informatie WHERE sessie_id = '$sessie_id'";
        $result = $conn->query($checkQuery);
        $row = $result->fetch_assoc();

        if ($row['count'] > 0) {
            // Sessie-ID bestaat al, handle dit (bijwerken of invoer voorkomen)
            // Bijvoorbeeld, je zou het bestaande record kunnen bijwerken in plaats van een nieuw toe te voegen
            $updateQuery = "UPDATE informatie SET naam = '$naam', date = '$date', korte_text = '$korte_text', afbeelding = '$afbeelding' WHERE sessie_id = '$sessie_id'";
            if ($conn->query($updateQuery) === TRUE) {
                echo "Gegevens succesvol bijgewerkt!";
                $_SESSION['form_submitted'] = true;
            } else {
                echo "Fout bij bijwerken van gegevens: " . $conn->error;
            }

            // Optioneel: Je kunt het script hier beëindigen of doorverwijzen naar een andere pagina om dubbele inhoud te voorkomen
            exit();
        }

        // Voeg gegevens toe aan de database
        $sql = "INSERT INTO informatie (naam, voornaam, korte_text, afbeelding, sessie_id) VALUES ('$naam', '$date', '$korte_text', '$afbeelding', '$sessie_id')";

        if ($conn->query($sql) === TRUE) {
            echo "Gegevens succesvol toegevoegd!";
            $_SESSION['form_submitted'] = true;
        } else {
            echo "Fout bij toevoegen van gegevens: " . $conn->error;
        }
    }
}
?>


<h3>Messages</h3>
    <?php
    // Display messages
    $sql = "SELECT * FROM informatie ORDER BY id DESC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) { 
        while ($row = $result->fetch_assoc()) {
            echo "<p><strong>" . $row["naam"] . "<br> " . $row["date"] . " . <br>" . $row["korte_text"] . "</p>";
        }
    } else {
        echo "No messages found.";
    }

    // Close the database connection
    $conn->close();
    ?>
    </div>
</body>
</html>