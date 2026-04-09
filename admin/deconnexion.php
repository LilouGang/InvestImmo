<?php
session_start();
session_unset(); // Vide les variables de session
session_destroy(); // Détruit le badge
header("Location: index.php"); // Renvoie à l'accueil
exit();
?>