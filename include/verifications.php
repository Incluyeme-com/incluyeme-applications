<?php
include_once './lib/incluyeme_applications.php';
header('Content-type: application/json');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $_POST = json_decode(file_get_contents("php://input"), true);
    $verifications = new incluyeme_applications();
    if (isset($_POST['candidateSearch'])) {
        if (!empty($_POST['resultsNumbers'])) {
            $verifications->resultsNumbers = $_POST['resultsNumbers'] === 0 ? 1 : $_POST['resultsNumbers'];
        } else {
            $verifications->resultsNumbers = 1;
        }
        $verifications::setCandidate($_POST['name']);
        $verifications::setCandidateMail($_POST['email']);
        $verifications::setCandidateKey($_POST['keyword']);
        $response = $verifications->searchCandidate();
        echo $verifications->json_response(200, $response);
        return;
    } else if (isset($_POST['employerSearch'])) {
        if (!empty($_POST['resultsNumbers'])) {
            $verifications->resultsNumbers = $_POST['resultsNumbers'] === 0 ? 1 : $_POST['resultsNumbers'];
        } else {
            $verifications->resultsNumbers = 1;
        }
        $verifications::setEmployed($_POST['company']);
        $verifications::setJob($_POST['job']);
        $verifications::setJobId($_POST['jobId']);
        $response = $verifications->searchEmployee();
        echo $verifications->json_response(200, $response);
        return;
    } else if (isset($_POST['appApplications'])) {
        if (!empty($_POST['resultsNumbers'])) {
            $verifications->resultsNumbers = $_POST['resultsNumbers'] === 0 ? 1 : $_POST['resultsNumbers'];
        } else {
            $verifications->resultsNumbers = 1;
        }
        $verifications::setCandidates($_POST['applicants']);
        $verifications::setJobs($_POST['jobs']);
        $verifications::setApplicationMessage($_POST['textApplication']);
        $verifications::setMessage($_POST['textEmail']);
        $response = $verifications->appApplications();
        echo $verifications->json_response(200, $response);
        return;
    }
}