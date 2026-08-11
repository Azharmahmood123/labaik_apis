<?php
declare(strict_types=1);

function jsonResponse(
    bool $success,
    mixed $data = [],
    string $message = "OK",
    int $status = 200
): never {

    http_response_code($status);

    echo json_encode(
        [
            "success" => $success,
            "message" => $message,
            "data" => $data
        ],
        JSON_UNESCAPED_UNICODE |
        JSON_UNESCAPED_SLASHES
    );

    exit;
}