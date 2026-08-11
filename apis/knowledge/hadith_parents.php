<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

requireMethod('GET');

try {
    $result = $conn->query("
        SELECT
            parent_hadith_id     AS id,
            parent_hadith_title  AS title,
            parent_hadith_arabic AS arabic,
            parent_hadith_eng    AS english
        FROM hadith_parents_table
        ORDER BY parent_hadith_id ASC
    ");

    $hadiths = fetchAll($result);

    jsonResponse(
        true,
        [
            "hadiths" => $hadiths
        ],
        message: "Successfully retrieved parent Hadith data"
    );
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());

    jsonResponse(
        false,
        [],
        "Failed to retrieve parent Hadith data.",
        500
    );
}