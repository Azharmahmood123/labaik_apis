<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

requireMethod('GET');

try {
    $result = $conn->query("
        SELECT
            id,
            title,
            arabic,
            transliteration,
            eng_translation AS engTranslation,
            urdu_translation AS urduTranslation,
            meaning,
            audio_name AS audioName
        FROM six_kalmas
        ORDER BY id ASC
    ");

    $kalmas = fetchAll($result);

    jsonResponse(
        true,
        [
            "sixKalmas" => $kalmas
        ],
        message: "Successfully retrieved Six Kalmas"
    );
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());

    jsonResponse(
        false,
        [],
        "Failed to retrieve Six Kalmas data.",
        500
    );
}