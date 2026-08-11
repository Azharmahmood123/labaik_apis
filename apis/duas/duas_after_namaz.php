<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

requireMethod('GET');

try {
    $result = $conn->query("
        SELECT
            dua_id AS id,
            dua_arabic AS arabic,
            dua_transliteration AS transliteration,
            dua_translation AS translation,
            dua_reference AS reference,
            dua_benefit_virtues AS benefitVirtues
        FROM duas_after_namaz
        ORDER BY dua_id ASC
    ");

    $duas = fetchAll($result);

    jsonResponse(
        true,
        [
            "duas" => $duas
        ],
        message: "Successfully retrieved duas after namaz"
    );
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());

    jsonResponse(
        false,
        [],
        "Failed to retrieve duas after namaz data.",
        500
    );
}