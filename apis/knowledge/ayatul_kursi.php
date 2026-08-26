<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

requireMethod('GET');

try {
    // Query Execution
    $stmt = $conn->prepare("
        SELECT
            id,
            arabic,
            transliteration,
            en_translation AS enTranslation,
            ur_translation AS urTranslation,
            hi_translation AS hiTranslation,
            id_translation AS idTranslation,
            ar_translation AS arTranslation,
            bn_translation AS bnTranslation,
            tr_translation AS trTranslation,
            fa_translation AS faTranslation,
            fr_translation AS frTranslation
        FROM ayatul_kursi
        ORDER BY id ASC
    ");

    if (!$stmt) {
        jsonResponse(
            false,
            [],
            "Failed to retrieve Ayat Al-Kursi data",
            500
        );
    }

    $stmt->execute();

    $result = $stmt->get_result();
    $verses = fetchAll($result);
    $stmt->close();

    jsonResponse(
        true,
        [
            "verses" => $verses
        ],
        message: "Successfully retrieved verses"
    );
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());

    jsonResponse(
        false,
        [],
        "Failed to retrieve Ayat Al-Kursi data",
        500
    );
}