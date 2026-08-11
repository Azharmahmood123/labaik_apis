<?php
declare(strict_types=1);

class Validator
{
    private array $errors = [];

    public function validate(array $data, array $rules): bool
    {
        $this->errors = [];

        foreach ($rules as $field => $ruleString) {
            $fieldRules = explode('|', $ruleString);
            $value = $data[$field] ?? null;

            foreach ($fieldRules as $rule) {
                $params = [];
                if (str_contains($rule, ':')) {
                    [$rule, $paramString] = explode(':', $rule, 2);
                    $params = explode(',', $paramString);
                }

                $this->applyRule($field, $value, $rule, $params);
            }
        }

        return empty($this->errors);
    }

    private function applyRule(string $field, mixed $value, string $rule, array $params): void
    {
        if ($rule === 'required' && ($value === null || $value === '')) {
            $this->errors[$field][] = "The {$field} field is required.";
            return;
        }

        // Skip other rules if field is optional and missing
        if ($value === null || $value === '') {
            return;
        }

        switch ($rule) {
            case 'int':
                if (filter_var($value, FILTER_VALIDATE_INT) === false) {
                    $this->errors[$field][] = "The {$field} must be an integer.";
                }
                break;

            case 'min':
                if ((int)$value < (int)$params[0]) {
                    $this->errors[$field][] = "The {$field} must be at least {$params[0]}.";
                }
                break;

            case 'max':
                if ((int)$value > (int)$params[0]) {
                    $this->errors[$field][] = "The {$field} may not be greater than {$params[0]}.";
                }
                break;

            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->errors[$field][] = "The {$field} must be a valid email address.";
                }
                break;
        }
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}