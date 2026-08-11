<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

requireMethod('GET');

try {
    $result = $conn->query("
        SELECT
            id,
            title_en     AS title,
            hadith_ar    AS arabic,
            hadith_en    AS english,
            reference_ar AS referenceArabic,
            reference_en AS referenceEnglish
        FROM hadith_qudsi_table
        ORDER BY id ASC
    ");

    $hadiths = fetchAll($result);

    jsonResponse(
        true,
        [
            "hadiths" => $hadiths
        ],
        message: "Successfully retrieved Hadith Qudsi data"
    );
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());

    jsonResponse(
        false,
        [],
        "Failed to retrieve Hadith Qudsi data.",
        500
    );
}