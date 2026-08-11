<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

requireMethod('GET');

try {
    $result = $conn->query("
        SELECT
            hadith_id        AS id,
            hadith_ar        AS arabic,
            hadith_en        AS english,
            hadith_reference AS reference
        FROM hadith_dehlawi_table
        ORDER BY hadith_id ASC
    ");

    $hadiths = fetchAll($result);

    jsonResponse(
        true,
        [
            "hadiths" => $hadiths
        ],
        message: "Successfully retrieved Hadith data"
    );
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());

    jsonResponse(
        false,
        [],
        "Failed to retrieve Hadith data.",
        500
    );
}