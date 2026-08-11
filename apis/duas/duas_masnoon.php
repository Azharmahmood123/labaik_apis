<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

requireMethod('GET');

try {
    $result = $conn->query("
        SELECT
            dua_id AS id,
            dua_arabic AS arabic,
            dua_title_eng AS titleEnglish,
            dua_title_urdu AS titleUrdu,
            dua_english_translation AS translationEnglish,
            dua_urdu_translation AS translationUrdu
        FROM duas_masnoon
        ORDER BY dua_id ASC
    ");

    $duas = fetchAll($result);

    jsonResponse(
        true,
        [
            "duas" => $duas
        ],
        message: "Successfully retrieved masnoon duas"
    );
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());

    jsonResponse(
        false,
        [],
        "Failed to retrieve masnoon duas data.",
        500
    );
}