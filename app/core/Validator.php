<?php

defined('ROOTPATH') OR exit('Access Denied!');

/**
 * API Validation class
 */
if (!class_exists('Validator')) {
class Validator
{
    protected $data = [];
    protected $rules = [];
    protected $errors = [];

    public function __construct($data = [])
    {
        $this->data = $data;
    }

    public static function make($data, $rules)
    {
        $validator = new self($data);
        return $validator->validate($rules);
    }

    public function validate($rules)
    {
        $this->rules = $rules;
        $this->errors = [];

        foreach ($rules as $field => $fieldRules) {
            $this->validateField($field, $fieldRules);
        }

        return $this;
    }

    protected function validateField($field, $rules)
    {
        $rules = is_string($rules) ? explode('|', $rules) : $rules;
        $value = $this->data[$field] ?? null;

        foreach ($rules as $rule) {
            $this->applyRule($field, $rule, $value);
        }
    }

    protected function applyRule($field, $rule, $value)
    {
        // Parse rule with parameters (e.g., "min:5")
        $parts = explode(':', $rule, 2);
        $ruleName = $parts[0];
        $parameter = $parts[1] ?? null;

        switch ($ruleName) {
            case 'required':
                if (empty($value) && $value !== '0') {
                    $this->addError($field, "The {$field} field is required");
                }
                break;

            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, "The {$field} must be a valid email address");
                }
                break;

            case 'min':
                if (!empty($value) && strlen($value) < $parameter) {
                    $this->addError($field, "The {$field} must be at least {$parameter} characters");
                }
                break;

            case 'max':
                if (!empty($value) && strlen($value) > $parameter) {
                    $this->addError($field, "The {$field} may not be greater than {$parameter} characters");
                }
                break;

            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    $this->addError($field, "The {$field} must be a number");
                }
                break;

            case 'alpha':
                if (!empty($value) && !preg_match('/^[a-zA-Z]+$/', $value)) {
                    $this->addError($field, "The {$field} may only contain letters");
                }
                break;

            case 'alpha_num':
                if (!empty($value) && !preg_match('/^[a-zA-Z0-9]+$/', $value)) {
                    $this->addError($field, "The {$field} may only contain letters and numbers");
                }
                break;

            case 'in':
                $allowed = explode(',', $parameter);
                if (!empty($value) && !in_array($value, $allowed)) {
                    $this->addError($field, "The selected {$field} is invalid");
                }
                break;
        }
    }

    protected function addError($field, $message)
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }

    public function fails()
    {
        return !empty($this->errors);
    }

    public function passes()
    {
        return empty($this->errors);
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getFirstError($field = null)
    {
        if ($field) {
            return $this->errors[$field][0] ?? null;
        }

        foreach ($this->errors as $fieldErrors) {
            return $fieldErrors[0] ?? null;
        }

        return null;
    }
}
}