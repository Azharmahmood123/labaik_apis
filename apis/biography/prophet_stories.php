<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

requireMethod('GET');

try {
    $result = $conn->query("
        SELECT
            story_id AS id,
            story_title AS title,
            story_description AS description
        FROM quran_stories
        ORDER BY story_id ASC
    ");

    if (!$result) {
        jsonResponse(
            false,
            [],
            "Failed to retrieve Quran stories data",
            500
        );
    }

    $stories = fetchAll($result);

    jsonResponse(
        true,
        [
            "stories" => $stories
        ],
        message: "Successfully retrieved Prophet & Quran stories"
    );
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());

    jsonResponse(
        false,
        [],
        "Failed to retrieve Quran stories data",
        500
    );
}