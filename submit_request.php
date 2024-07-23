<?php
// Activer le rapport d'erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $employeeName = $_POST['employeeName'];
    $providerName = $_POST['providerName'];
    $courseCode = $_POST['courseCode'];
    $courseTitle = $_POST['courseTitle'];
    $registrationDeadline = $_POST['registrationDeadline'];
    $category = $_POST['category'];
    $modality = $_POST['modality'];
    $language = $_POST['language'];
    $courseDuration = $_POST['courseDuration'];
    $courseFee = $_POST['courseFee'];
    $courseStartDate = $_POST['courseStartDate'];

    // Validation des données
    if (empty($employeeName) || empty($providerName) || empty($courseCode) || empty($courseTitle) || empty($registrationDeadline) || empty($category) || empty($modality) || empty($language) || empty($courseDuration) || empty($courseFee) || empty($courseStartDate)) {
        echo json_encode(['success' => false, 'message' => 'Tous les champs doivent être remplis.']);
        exit;
    }

    // Connexion à la base de données
    $conn = new mysqli('localhost', 'username', 'password', 'database');
    
    if ($conn->connect_error) {
        echo json_encode(['success' => false, 'message' => 'Erreur de connexion à la base de données : ' . $conn->connect_error]);
        exit;
    }

    // Insertion des données dans la base de données
    $stmt = $conn->prepare("INSERT INTO training_requests (employee_name, provider_name, course_code, course_title, registration_deadline, category, modality, language, course_duration, course_fee, course_start_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('ssssssssiii', $employeeName, $providerName, $courseCode, $courseTitle, $registrationDeadline, $category, $modality, $language, $courseDuration, $courseFee, $courseStartDate);

    if ($stmt->execute()) {
        // Envoi du courriel de notification
        $to = 'ChBerge@lacitec.on.ca';
        $subject = 'Nouvelle demande de formation';
        $message = "Une nouvelle demande de formation a été soumise par $employeeName.\n\nDétails du cours:\n\nTitre: $courseTitle\nFournisseur: $providerName\nDate de début: $courseStartDate";
        $headers = 'From: founder@dollarsage.com';

        if (mail($to, $subject, $message, $headers)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'envoi du courriel.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la soumission de la demande : ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
?>
