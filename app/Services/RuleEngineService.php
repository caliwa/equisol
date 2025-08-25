<?php

namespace App\Services;

class RuleEngineService
{
    public function process(array $ruleSet, array $data)
    {
        foreach ($ruleSet['rules'] as $rule) {
            if ($this->conditionsMet($rule['conditions'], $data)) {
                return $rule['result'];
            }
        }
        return $ruleSet['default_value'] ?? 0;
    }

    private function conditionsMet(array $conditions, array $data): bool
    {
        foreach ($conditions as $condition) {
            $variableName = $condition['variable'];
            $operator = $condition['operator'];
            $value = $condition['value'];

            if (!isset($data[$variableName])) {
                return false;
            }

            $inputValue = $data[$variableName];

            if (!$this->evaluateCondition($inputValue, $operator, $value)) {
                return false;
            }
        }
        return true;
    }

    private function evaluateCondition($inputValue, $operator, $value): bool
    {
        return match ($operator) {
            '==' => $inputValue == $value,
            '!=' => $inputValue != $value,
            '>' => $inputValue > $value,
            '>=' => $inputValue >= $value,
            '<' => $inputValue < $value,
            '<=' => $inputValue <= $value,
            default => false,
        };
    }
}