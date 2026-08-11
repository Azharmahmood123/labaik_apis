<?php
declare(strict_types=1);

function cleanRow(array $row): array
{
    foreach ($row as $key => $value) {

        if ($value === null || $value === "") {
            unset($row[$key]);
        }

    }

    return $row;
}

function fetchAll(mysqli_result $result): array
{
    $rows = [];

    while ($row = $result->fetch_assoc()) {
        $rows[] = cleanRow($row);
    }

    return $rows;
}

function requireMethod(string|array $methods): void
{
    $methods = (array)$methods;

    $current = strtoupper($_SERVER['REQUEST_METHOD']);

    $allowed = array_map('strtoupper', $methods);

    if (!in_array($current, $allowed, true)) {

        header('Allow: '.implode(', ', $allowed));

        jsonResponse(
            false,
            [],
            "Method not allowed",
            405
        );
    }
}

function input(string $key, mixed $default = null): mixed
{
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'GET') {
        return $_GET[$key] ?? $default;
    }

    if ($method === 'POST') {

        if (!empty($_POST)) {
            return $_POST[$key] ?? $default;
        }

        $json = json_decode(file_get_contents('php://input'), true);

        return $json[$key] ?? $default;
    }

    return $default;
}