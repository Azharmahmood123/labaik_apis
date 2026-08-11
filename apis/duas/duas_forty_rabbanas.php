<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

requireMethod('GET');

try {
    $result = $conn->query("
        SELECT
            dua_id AS id,
            dua_title AS title,
            dua_arabic AS arabic,
            dua_transliteration AS transliteration,
            dua_english_translation AS englishTranslation,
            dua_urdu_translation AS urduTranslation,
            dua_reference AS reference,
            dua_additional_info AS additionalInfo,
            dua_audio_name AS audioName
        FROM duas_forty_rabbanas
        ORDER BY dua_id ASC
    ");

    $duas = fetchAll($result);

    jsonResponse(
        true,
        [
            "duas" => $duas
        ],
        message: "Successfully retrieved forty rabbanas duas"
    );
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());

    jsonResponse(
        false,
        [],
        "Failed to retrieve forty rabbanas duas data.",
        500
    );
}