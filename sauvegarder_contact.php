<?php
require 'includes/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // On cherche la valeur avec Majuscule, et sinon avec minuscule pour être sûr
    $prenom = htmlspecialchars($_POST['Prenom'] ?? $_POST['prenom'] ?? '');
    $nom = htmlspecialchars($_POST['Nom'] ?? $_POST['nom'] ?? 'Inconnu');
    $email = htmlspecialchars($_POST['Email'] ?? $_POST['email'] ?? '');
    $telephone = htmlspecialchars($_POST['Telephone'] ?? $_POST['telephone'] ?? '');
    $sujet = htmlspecialchars($_POST['Sujet'] ?? $_POST['sujet'] ?? 'Demande de contact');
    $message = htmlspecialchars($_POST['Message'] ?? $_POST['message'] ?? '');

    if (!empty($email) && !empty($message)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO messages_contact (prenom, nom, email, telephone, sujet, message) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$prenom, $nom, $email, $telephone, $sujet, $message]);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Données incomplètes']);
    }
}