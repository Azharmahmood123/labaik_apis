<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

requireMethod('GET');

try {
    $result = $conn->query("
        SELECT
            hadith_id          AS id,
            hadith_no          AS number,
            hadith_narrated    AS narratedBy,
            hadith_description AS description
        FROM hadith_sacred_110_table
        ORDER BY hadith_id ASC
    ");

    $hadiths = fetchAll($result);

    jsonResponse(
        true,
        [
            "hadiths" => $hadiths
        ],
        message: "Successfully retrieved 110 Sacred Hadith data"
    );
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());

    jsonResponse(
        false,
        [],
        "Failed to retrieve 110 Sacred Hadith data.",
        500
    );
}