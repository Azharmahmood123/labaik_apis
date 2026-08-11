<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

requireMethod('GET');

try {
    $result = $conn->query("
        SELECT
            hadith_id AS id,
            hadith_ar AS arabic,
            hadith_en AS english
        FROM hadith_quran_table
        ORDER BY hadith_id ASC
    ");

    $hadiths = fetchAll($result);

    jsonResponse(
        true,
        [
            "hadiths" => $hadiths
        ],
        message: "Successfully retrieved Quran Hadith data"
    );
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());

    jsonResponse(
        false,
        [],
        "Failed to retrieve Quran Hadith data.",
        500
    );
}