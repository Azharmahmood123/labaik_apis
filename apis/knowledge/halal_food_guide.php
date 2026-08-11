<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

requireMethod('GET');

try {
    $result = $conn->query("
        SELECT
            id,
            heading,
            category,
            item,
            halal
        FROM halal_food_guide_table
        ORDER BY heading ASC, id ASC
    ");

    $grouped = [];

    while ($row = $result->fetch_assoc()) {
        $cleanedRow = cleanRow($row);
        $heading = !empty($cleanedRow['heading']) ? trim($cleanedRow['heading']) : "Others";

        unset($cleanedRow['heading']);

        if (!isset($grouped[$heading])) {
            $grouped[$heading] = [];
        }

        $grouped[$heading][] = $cleanedRow;
    }

    $foodGuideList = [];

    foreach ($grouped as $heading => $items) {
        $foodGuideList[] = [
            "heading" => $heading,
            "items"   => $items
        ];
    }

    jsonResponse(
        true,
        $foodGuideList,
        message: "Successfully retrieved Halal food guide data"
    );
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());

    jsonResponse(
        false,
        [],
        "Failed to retrieve Halal food guide data.",
        500
    );
}