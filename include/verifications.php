<?php
include_once './lib/incluyeme_applications.php';
header('Content-type: application/json');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $_POST = json_decode(file_get_contents("php://input"), true);
    $verifications = new incluyeme_applications();
    error_log(print_r($_POST, true));
    if (isset($_POST['candidateSearch'])) {
        $verifications::setCandidate($_POST['name']);
        $verifications::setCandidateMail($_POST['emailCan']);
        $verifications::setCandidateKey($_POST['keyword']);
        $response = $verifications->searchCandidate();
        echo $verifications->json_response(200, $response);
        return;
    }
}