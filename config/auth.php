<?php
declare(strict_types=1);

function authenticate(): void
{
    $headers = [];
    if (function_exists("getallheaders")) {
        $headers = array_change_key_case(getallheaders(), CASE_UPPER);
    }

    $key = $headers["X-API-KEY"] ?? $_SERVER["HTTP_X_API_KEY"] ?? "";
    $secret = $headers["X-API-SECRET"] ?? $_SERVER["HTTP_X_API_SECRET"] ?? "";

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
