<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

requireMethod('GET');

try {
    $result = $conn->query("
        SELECT
            saying_id AS id,
            saying_heading AS heading,
            saying_reported AS reported,
            saying_narration AS narration,
            saying_cited AS cited
        FROM sayings_salaf
        ORDER BY saying_id ASC
    ");

    $data = fetchAll($result);

    jsonResponse(
        true,
        [
            "sayings" => $data
        ],
        message: "Successfully retrieved Salaf sayings"
    );
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());

    jsonResponse(
        false,
        [],
        "Failed to retrieve Salaf sayings data.",
        500
    );
}