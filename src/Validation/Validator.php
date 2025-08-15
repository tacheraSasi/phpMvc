<?php

namespace Viper\Validation;

defined('ROOTPATH') OR exit('Access Denied!');

/**
 * DTO Validation class
 */
class Validator
{
    protected array $rules = [];
    protected array $errors = [];
    protected array $data = [];
    
    public function __construct(array $data)
    {
        $this->data = $data;
    }
    
    /**
     * Set validation rules
     */
    public function rules(array $rules): self
    {
        $this->rules = $rules;
        return $this;
    }
    
    /**
     * Validate the data
     */
    public function validate(): bool
    {
        $this->errors = [];
        
        foreach ($this->rules as $field => $fieldRules) {
            $this->validateField($field, $fieldRules);
        }
        
        return empty($this->errors);
    }
    
    /**
     * Validate a single field
     */
    protected function validateField(string $field, array|string $rules): void
    {
        if (is_string($rules)) {
            $rules = explode('|', $rules);
        }
        
        $value = $this->data[$field] ?? null;
        
        foreach ($rules as $rule) {
            $this->applyRule($field, $value, $rule);
        }
    }
    
    /**
     * Apply a validation rule
     */
    protected function applyRule(string $field, mixed $value, string $rule): void
    {
        $ruleParts = explode(':', $rule);
        $ruleName = $ruleParts[0];
        $parameters = isset($ruleParts[1]) ? explode(',', $ruleParts[1]) : [];
        
        switch ($ruleName) {
            case 'required':
                if (empty($value)) {
                    $this->addError($field, "$field is required");
                }
                break;
                
            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, "$field must be a valid email address");
                }
                break;
                
            case 'min':
                $min = (int) ($parameters[0] ?? 0);
                if (!empty($value)) {
                    if (is_numeric($value)) {
                        if ((int) $value < $min) {
                            $this->addError($field, "$field must be at least $min");
                        }
                    } else {
                        if (strlen($value) < $min) {
                            $this->addError($field, "$field must be at least $min characters");
                        }
                    }
                }
                break;
                
            case 'max':
                $max = (int) ($parameters[0] ?? 0);
                if (!empty($value) && strlen($value) > $max) {
                    $this->addError($field, "$field must not exceed $max characters");
                }
                break;
                
            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    $this->addError($field, "$field must be numeric");
                }
                break;
                
            case 'alpha':
                if (!empty($value) && !preg_match('/^[a-zA-Z]+$/', $value)) {
                    $this->addError($field, "$field must contain only letters");
                }
                break;
                
            case 'alpha_num':
                if (!empty($value) && !preg_match('/^[a-zA-Z0-9]+$/', $value)) {
                    $this->addError($field, "$field must contain only letters and numbers");
                }
                break;
                
            case 'in':
                if (!empty($value) && !in_array($value, $parameters)) {
                    $allowedValues = implode(', ', $parameters);
                    $this->addError($field, "$field must be one of: $allowedValues");
                }
                break;
        }
    }
    
    /**
     * Add validation error
     */
    protected function addError(string $field, string $message): void
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        
        $this->errors[$field][] = $message;
    }
    
    /**
     * Get validation errors
     */
    public function errors(): array
    {
        return $this->errors;
    }
    
    /**
     * Get first error for a field
     */
    public function firstError(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }
    
    /**
     * Check if validation failed
     */
    public function fails(): bool
    {
        return !$this->validate();
    }
    
    /**
     * Create validator instance
     */
    public static function make(array $data, array $rules): self
    {
        return (new self($data))->rules($rules);
    }
}