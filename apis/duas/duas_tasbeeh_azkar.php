<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

requireMethod('GET');

try {
    $result = $conn->query("
        SELECT
            tasbeeh_id AS id,
            tasbeeh_arabic AS arabic,
            tasbeeh_reference AS reference,
            tasbeeh_transliteration AS transliteration,
            tasbeeh_english_translation AS englishTranslation,
            tasbeeh_no_total_count AS noTotalCount,
            tasbeeh_counter AS counter,
            tasbeeh_total AS total
        FROM duas_tasbeeh_azkar
        ORDER BY tasbeeh_id ASC
    ");

    $duas = fetchAll($result);

    jsonResponse(
        true,
        [
            "duas" => $duas
        ],
        message: "Successfully retrieved tasbeeh azkar"
    );
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());

    jsonResponse(
        false,
        [],
        "Failed to retrieve tasbeeh azkar data.",
        500
    );
}