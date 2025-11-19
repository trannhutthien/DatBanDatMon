<?php
/**
 * Validator Class
 * Simple validation
 */

namespace App\Core;

class Validator
{
    private array $errors = [];

    public function validate(array $data, array $rules): bool
    {
        $this->errors = [];

        foreach ($rules as $field => $ruleSet) {
            $ruleArray = explode('|', $ruleSet);
            $value = $data[$field] ?? null;

            foreach ($ruleArray as $rule) {
                $this->applyRule($field, $value, $rule, $data);
            }
        }

        return empty($this->errors);
    }

    private function applyRule(string $field, $value, string $rule, array $data): void
    {
        if (strpos($rule, ':') !== false) {
            [$ruleName, $ruleValue] = explode(':', $rule, 2);
        } else {
            $ruleName = $rule;
            $ruleValue = null;
        }

        switch ($ruleName) {
            case 'required':
                if (empty($value) && $value !== '0') {
                    $this->errors[$field][] = "$field is required";
                }
                break;

            case 'email':
                if ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->errors[$field][] = "$field must be a valid email";
                }
                break;

            case 'min':
                if ($value && strlen($value) < $ruleValue) {
                    $this->errors[$field][] = "$field must be at least $ruleValue characters";
                }
                break;

            case 'max':
                if ($value && strlen($value) > $ruleValue) {
                    $this->errors[$field][] = "$field must not exceed $ruleValue characters";
                }
                break;

            case 'numeric':
                if ($value && !is_numeric($value)) {
                    $this->errors[$field][] = "$field must be numeric";
                }
                break;

            case 'confirmed':
                $confirmField = $field . '_confirmation';
                if ($value !== ($data[$confirmField] ?? null)) {
                    $this->errors[$field][] = "$field confirmation does not match";
                }
                break;
        }
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(): ?string
    {
        foreach ($this->errors as $fieldErrors) {
            return $fieldErrors[0] ?? null;
        }
        return null;
    }
}
