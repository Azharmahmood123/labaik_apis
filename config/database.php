<?php
declare(strict_types=1);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {

    $conn = new mysqli(
        DB_HOST,
        DB_USER,
        DB_PASS,
        DB_NAME
    );

    $conn->set_charset("utf8mb4");

} catch (Exception $e) {

    jsonResponse(
        false,
        [],
        "Database connection failed",
        500
    );

}