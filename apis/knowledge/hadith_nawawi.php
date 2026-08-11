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
            reference_ar AS referenceArabic,
            hadith_en    AS english,
            reference_en AS referenceEnglish
        FROM hadith_nawawi_table
        ORDER BY id ASC
    ");

    $hadiths = fetchAll($result);

    jsonResponse(
        true,
        [
            "hadiths" => $hadiths
        ],
        message: "Successfully retrieved 40 Hadith Nawawi data"
    );
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());

    jsonResponse(
        false,
        [],
        "Failed to retrieve 40 Hadith Nawawi data.",
        500
    );
}