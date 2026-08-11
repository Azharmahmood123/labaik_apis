<?php
declare(strict_types=1);

class ApiRequest
{
    private array $data = [];
    private string $method;

    public function __construct()
    {
        $this->method = strtoupper($_SERVER['REQUEST_METHOD']);
        $this->parseInput();
    }

    private function parseInput(): void
    {
        // 1. Always merge GET params
        $this->data = $_GET;

        // 2. Parse POST / PUT / PATCH body
        if (in_array($this->method, ['POST', 'PUT', 'PATCH'], true)) {
            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

            if (str_contains($contentType, 'application/json')) {
                $raw = file_get_contents('php://input');
                $json = json_decode($raw, true) ?? [];
                $this->data = array_merge($this->data, $json);
            } else {
                $this->data = array_merge($this->data, $_POST);
            }
        }
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    public function getInt(string $key, ?int $default = null): ?int
    {
        $val = $this->get($key);
        return filter_var($val, FILTER_VALIDATE_INT) !== false ? (int)$val : $default;
    }

    public function all(): array
    {
        return $this->data;
    }
}