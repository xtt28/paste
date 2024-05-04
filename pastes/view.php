<?php
$title = "Viewing paste";
include "../layout/top.php";
include "../db.php";

function handle(): void
{
    global $conn, $err, $result;

    if ($_SERVER["REQUEST_METHOD"] !== "GET") {
        $err = "Invalid request method.";
        http_response_code(405);
        return;
    }

    $paste_id = intval($_GET["id"]);
    if ($paste_id < 1) {
        $err = "Invalid ID.";
        http_response_code(400);
        return;
    }
    $stmt = $conn->prepare("SELECT * FROM `available_pastes` WHERE `id` = :id LIMIT 1");
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
if (isset($err)) {
    echo $err;
    include "../layout/bottom.php";
    return;
}
?>

<section class="hero">
    <div class="hero-body">
        <h1 class="title">Paste #<?= $result["id"] ?></h1>
        <p class="subtitle">Created <?= $result["created_at"] ?></p>
        <?php if (!empty($_GET["token"])) : ?>
            <form action="/pastes/delete.php" method="post">
                <input type="hidden" name="id" value="<?= $_GET["id"] ?>">
                <input type="hidden" name="delete-token" value="<?= $_GET["token"] ?>">
                <button class="button is-danger">Delete</button>
            </form>
        <?php endif ?>
    </div>
</section>

<pre><?= htmlspecialchars($result["content"]) ?></pre>

<?php include "../layout/bottom.php" ?>