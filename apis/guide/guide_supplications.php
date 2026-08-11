<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

requireMethod('GET');

try {
    $result = $conn->query("
        SELECT
            id,
            title,
            detail
        FROM guide_supplications
        ORDER BY id ASC
    ");

    if (!$result) {
        jsonResponse(
            false,
            [],
            "Failed to retrieve supplications guide data",
            500
        );
    }

    $items = fetchAll($result);

    jsonResponse(
        true,
        [
            "items" => $items
        ],
        message: "Successfully retrieved supplications guide data"
    );
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());

    jsonResponse(
        false,
        [],
        "Failed to retrieve supplications guide data",
        500
    );
}
