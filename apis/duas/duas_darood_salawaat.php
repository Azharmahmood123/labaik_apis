<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

requireMethod('GET');

try {
    $result = $conn->query("
        SELECT
            salawaat_id AS id,
            salawaat_title_ar AS titleArabic,
            salawaat_title_en AS titleEnglish,
            salawaat_arabic AS arabic,
            salawaat_transliteration AS transliteration,
            salawaat_translation AS translation,
            salawaat_reference AS reference
        FROM duas_darood_salawaat
        ORDER BY salawaat_id ASC
    ");

    $duas = fetchAll($result);

    jsonResponse(
        true,
        [
            "duas" => $duas
        ],
        message: "Successfully retrieved darood salawaat"
    );
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());

    jsonResponse(
        false,
        [],
        "Failed to retrieve darood salawaat data.",
        500
    );
}