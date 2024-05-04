<?php
include "../db.php";

function handle(): void
{
    global $conn, $err;

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        $err = "Invalid request method.";
        http_response_code(405);
        return;
    }

    $paste_id = intval($_POST["id"]);
    if ($paste_id < 1) {
        $err = "Invalid ID.";
        http_response_code(400);
        return;
    }
    $token = $_POST["delete-token"];
    if (empty($token)) {
        $err = "No deletion token provided.";
        http_response_code(400);
        return;
    }

    $stmt = $conn->prepare("DELETE FROM `pastes` WHERE `id` = :id AND `delete_token` = :token");
    $stmt->execute(["id" => $paste_id, "token" => $token]);

    if ($stmt->rowCount() === 0) {
        $err = "Could not delete paste (invalid ID or token).";
        http_response_code(500);
        return;
    }

    header("Location: /pastes/create.php");
}

handle();
if (isset($err)) {
    echo $err;
}
?>