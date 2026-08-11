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
        FROM guide_ihram
        ORDER BY id ASC
    ");

    if (!$result) {
        jsonResponse(
            false,
            [],
            "Failed to retrieve Ihram guide data",
            500
        );
    }

    $items = fetchAll($result);

    jsonResponse(
        true,
        [
            "items" => $items
        ],
        message: "Successfully retrieved Ihram guide data"
    );
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());

    jsonResponse(
        false,
        [],
        "Failed to retrieve Ihram guide data",
        500
    );
}
