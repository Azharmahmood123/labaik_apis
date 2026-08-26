<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

requireMethod('GET');

try {
    $result = $conn->query("
        SELECT
            id,
            name_arabic          AS nameArabic,
            name_transliteration AS nameTransliteration,
            meaning_english      AS meaningEnglish,
            meaning_urdu         AS meaningUrdu,
            meaning_short        AS meaningShort,
            meaning_long         AS meaningLong,
            benefit_english      AS benefitEnglish,
            benefit_urdu         AS benefitUrdu,
            verse_reference      AS verseReference
        FROM names_allah
        ORDER BY id ASC
    ");

    $names = fetchAll($result);

    jsonResponse(
        true,
        $names,
        message: "Successfully retrieved Allah Names data"
    );
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());

    jsonResponse(
        false,
        [],
        "Failed to retrieve Allah Names data.",
        500
    );
}