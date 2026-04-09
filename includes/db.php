<?php
// Remplace ces valeurs par celles que Hostinger t'a données à l'Étape 2
$host = "localhost"; // Chez Hostinger, ça reste 99% du temps "localhost"
$dbname = "u124752770_AvenirImmo";
$username = "u124752770_etna";
$password = "ZpRQZv4QYpAkp69";

try {
    // On essaie de se connecter
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // On demande à PHP d'afficher les erreurs si ça plante (pour nous aider à coder)
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Erreur de connexion à la base de données : " . $e->getMessage();
    die();
}
?>