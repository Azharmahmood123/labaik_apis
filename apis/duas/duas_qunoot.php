<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

requireMethod('GET');

try {
    $result = $conn->query("
        SELECT
            id,
            arabic,
            engTranslations AS englishTranslation,
            urduTranslations AS urduTranslation,
            transliteration
        FROM duas_qunoot
        ORDER BY id ASC
    ");

    $duas = fetchAll($result);

    jsonResponse(
        true,
        [
            "duas" => $duas
        ],
        message: "Successfully retrieved qunoot duas"
    );
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());

    jsonResponse(
        false,
        [],
        "Failed to retrieve qunoot duas data.",
        500
    );
}