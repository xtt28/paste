<?php
include "../layout/top.php";
include "../db.php";

$today = date("Y-m-d");

function is_date_valid(string $date): bool
{
    $as_timestamp = strtotime($date);
    if (!$as_timestamp) {
        return false;
    }

    return date("Y-m-d", $as_timestamp) === $date;
}

function handle(): void
{
    global $conn, $err, $today;

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        return;
    }

    $content = $_POST["content"];
    $expiry = $_POST["expires-at"];

    if (empty($content) || (!empty($expiry) && !is_date_valid($expiry))) {
        $err = "Invalid POST data.";
        http_response_code(400);
        return;
    }

    if ($expiry < $today) {
        $err = "Expiration date is too early.";
        http_response_code(400);
        return;
    }

    try {
        $stmt = $conn->prepare("INSERT INTO `pastes` (`expires_at`, `content`) VALUES (:expiry, :content)");
        $stmt->execute(["expiry" => $expiry ?: null, "content" => $content]);
    } catch (PDOException $e) {
        error_log("Could not create paste: $e");
        $err = "Could not save paste.";
        http_response_code(500);
        return;
    }

    $id = $conn->lastInsertId();
    try {
        $stmt = $conn->prepare("SELECT `delete_token` FROM `pastes` WHERE `id` = :id");
        $stmt->execute(["id" => $id]);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Could not query newly saved paste: $e");
        $err = "Could not get newly saved paste information.";
        http_response_code(500);
        return;
    }

    header("Location: /pastes/view.php?id=$id&token={$result["delete_token"]}");
}

handle();
?>

<h1>Create a paste</h1>
<?php if ($err): ?>
    <p><?= $err ?></p>
<?php endif ?>
<form action="/pastes/create.php" method="post">
    <div>
        <label for="content">Content</label>
        <div>
            <textarea name="content" id="content" required></textarea>
        </div>
    </div>
    <div>
        <label for="expires-at">Expiry date</label>
        <input type="date" name="expires-at" id="expires-at" min="<?= $today ?>">
    </div>
    <button>Create</button>
</form>

<?php include "../layout/bottom.php" ?>