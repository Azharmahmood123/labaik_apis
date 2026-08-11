<?php
declare(strict_types=1);

function authenticate(): void
{
    
    $headers = getallheaders();

    $key = $headers['X-API-KEY'] ?? '';
    $secret = $headers['X-API-SECRET'] ?? '';

    if (
        $key !== API_KEY ||
        $secret !== API_SECRET
    ) {

        jsonResponse(
            false,
            [],
            "Unauthorized",
            401
        );

    }
}