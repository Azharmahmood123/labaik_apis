<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

requireMethod('GET');

try {
    $result = $conn->query("
        SELECT
            id,
            category,
            content
        FROM ayatul_kursi_benefits
        ORDER BY id ASC
    ");

    $rows = fetchAll($result);

    $categorized = [
        "hadiths" => [],
        "importance" => [],
        "reciting" => []
    ];

    foreach ($rows as $row) {
        $category = strtolower(trim($row['category'] ?? ''));

        if (array_key_exists($category, $categorized)) {
            $categorized[$category][] = $row;
        }
    }

    jsonResponse(
        true,
        $categorized,
        message: "Successfully retrieved Ayat al-Kursi benefits"
    );
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());

    jsonResponse(
        false,
        [],
        "Failed to retrieve Ayat al-Kursi benefits data.",
        500
    );
}