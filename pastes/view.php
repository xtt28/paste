<?php
include "../layout/top.php";
include "../db.php";

function handle(): void
{
    global $conn, $err, $result;

    if (!$_SERVER["REQUEST_METHOD"] === "GET") {
        $err = "Invalid request method.";
        http_response_code(405);
        return;
    }

    $paste_id = $_GET["id"];
    $stmt = $conn->prepare("SELECT * FROM `pastes` WHERE `id` = :id LIMIT 1");
    $stmt->execute(["id" => $paste_id]);

    if ($stmt->rowCount() === 0) {
        $err = "Paste not found.";
        http_response_code(404);
        return;
    }

    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $result = $stmt->fetch();
}

handle();
?>

<?php if (empty($err)): ?>
    <pre><?= $result["content"] ?></pre>
<?php else: ?>
    Paste not found.
<?php endif ?>

<?php include "../layout/bottom.php" ?>