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
            enTranslation,
            urTranslation,
            enTafsir
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