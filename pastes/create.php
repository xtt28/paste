<?php
include "../layout/top.php";
include "../db.php";

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
    global $conn, $err;

    if (!$_SERVER["REQUEST_METHOD"] === "POST") {
        $err = "Invalid request method.";
        http_response_code(405);
        return;
    }

    $content = $_POST["content"];
    $expiry = $_POST["expires-at"];

    if (empty($content) || (!empty($expiry) && !is_date_valid($expiry))) {
        $err = "Invalid POST data.";
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

    header("Location: /pastes/view.php?id={$conn->lastInsertId()}");
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
        <input type="date" name="expires-at" id="expires-at" min="<?= (new DateTime("tomorrow"))->format("Y-m-d") ?>">
    </div>
    <button>Create</button>
</form>

<?php include "../layout/bottom.php" ?>