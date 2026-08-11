<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

requireMethod('GET');

try {
    $stmt = $conn->prepare("
        SELECT
            id,
            name_image AS nameImage,
            name_arabic AS nameArabic,
            name_transliteration AS nameTransliteration,
            name_audio AS nameAudio,
            meaning_english AS meaningEnglish,
            meaning_urdu AS meaningUrdu,
            benefit_english AS benefitEnglish,
            benefit_urdu AS benefitUrdu
        FROM names_muhammad
        ORDER BY id ASC
    ");

    if (!$stmt) {
        jsonResponse(
            false,
            [],
            "Failed to retrieve Muhammad names data.",
            500
        );
    }

    $stmt->execute();

    $result = $stmt->get_result();
    $names = fetchAll($result);
    $stmt->close();

    jsonResponse(
        true,
        [
            "data" => $names
        ],
        message: "Successfully retrieved Muhammad names data"
    );
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());

    jsonResponse(
        false,
        [],
        "Failed to retrieve Muhammad names data.",
        500
    );
}