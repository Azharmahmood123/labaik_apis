<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

requireMethod('GET');

try {
    $result = $conn->query("
        SELECT
            id,
            title,
            description
        FROM life_of_sahabas
        ORDER BY id ASC
    ");

    if (!$result) {
        jsonResponse(
            false,
            [],
            "Failed to retrieve Sahabas biography data",
            500
        );
    }

    $data = fetchAll($result);

    jsonResponse(
        true,
        [
            'sahabas' => $data
        ],
        message: 'Successfully retrieved Sahabas biography data'
    );
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());

    jsonResponse(
        false,
        [],
        'Failed to retrieve Sahabas biography data',
        500
    );
}