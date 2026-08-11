<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

requireMethod('GET');

try {
    $result = $conn->query("
        SELECT
            category_id AS id,
            category_title AS title,
            category_count AS count
        FROM duas_main_table
        ORDER BY category_id ASC
    ");

    $categories = fetchAll($result);

    jsonResponse(
        true,
        [
            "categories" => $categories
        ],
        message: "Successfully retrieved duas categories"
    );
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());

    jsonResponse(
        false,
        [],
        "Failed to retrieve duas categories data.",
        500
    );
}