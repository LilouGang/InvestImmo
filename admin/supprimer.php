<?php
session_start();
require '../includes/db.php';

// Vérification de sécurité
if (!isset($_SESSION['utilisateur_id']) || !isset($_GET['id'])) {
    header('Location: ../projets.php');
    exit();
}

$id_projet = (int)$_GET['id'];
$id_utilisateur = $_SESSION['utilisateur_id'];

// Vérifier que le projet appartient bien à l'utilisateur connecté
$stmtVerif = $pdo->prepare("SELECT id FROM projets WHERE id = ? AND id_utilisateur = ?");
$stmtVerif->execute([$id_projet, $id_utilisateur]);
if (!$stmtVerif->fetch()) {
    header('Location: ../projets.php');
    exit();
}

try {
    $pdo->beginTransaction();

    // 1. SUPPRESSION PHYSIQUE DES PHOTOS DU SERVEUR
    $stmtPhotos = $pdo->prepare("SELECT chemin_photo FROM projet_photos WHERE id_projet = ?");
    $stmtPhotos->execute([$id_projet]);
    while ($photo = $stmtPhotos->fetch()) {
        $chemin_photo = "../" . ltrim($photo['chemin_photo'], '../');
        if (file_exists($chemin_photo) && is_file($chemin_photo)) {
            unlink($chemin_photo);
        }
    }

    // 2. SUPPRESSION PHYSIQUE DES PLANS DU SERVEUR
    $stmtPlans = $pdo->prepare("SELECT fichier_url FROM projet_plans WHERE id_projet = ?");
    $stmtPlans->execute([$id_projet]);
    while ($plan = $stmtPlans->fetch()) {
        $chemin_plan = "../" . ltrim($plan['fichier_url'], '../');
        if (file_exists($chemin_plan) && is_file($chemin_plan)) {
            unlink($chemin_plan);
        }
    }

    // 3. SUPPRESSION DES DONNÉES EN BASE DE DONNÉES
    // L'ordre est important si vous n'avez pas activé les clés étrangères "ON DELETE CASCADE"
    $pdo->prepare("DELETE FROM projet_photos WHERE id_projet = ?")->execute([$id_projet]);
    $pdo->prepare("DELETE FROM projet_plans WHERE id_projet = ?")->execute([$id_projet]);
    $pdo->prepare("DELETE FROM projet_etapes WHERE id_projet = ?")->execute([$id_projet]);
    $pdo->prepare("DELETE FROM projets WHERE id = ?")->execute([$id_projet]);

    $pdo->commit();
    header('Location: ../projets.php');
    exit();

} catch (Exception $e) {
    $pdo->rollBack();
    die("Erreur lors de la suppression : " . $e->getMessage());
}