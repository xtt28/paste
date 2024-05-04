<?php
include "../layout/top.php";
include "../db.php";

function handle(): void
{
    global $conn, $err, $result;

    if (!$_SERVER["REQUEST_METHOD"] === "POST") {
        $err = "Invalid request method.";
        http_response_code(405);
        return;
    }


}

handle();
?>

<h1>Create a paste</h1>
<form action="/pastes/create.php" method="post">
    <div>
        <label for="content">Content</label>
        <div>
            <textarea name="content" id="content" required></textarea>
        </div>
    </div>
    <div>
        <label for="expires-at">Expiry date</label>
        <input type="date" name="expires-at" id="expires-at">
    </div>
    <button>Create</button>
</form>

<?php include "../layout/bottom.php" ?>