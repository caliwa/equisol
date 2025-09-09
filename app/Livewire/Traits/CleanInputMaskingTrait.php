<?php

namespace App\Livewire\Traits;

trait CleanInputMaskingTrait
{
    private function CleanInputMasking($numberString){
        if(is_null($numberString) || $numberString === ''){
            return null;
        }

        $decimals = $this->CountDecimals($numberString);

        $numberString = trim($numberString);

        $return_value = 0;
        
        if (empty($numberString) || !preg_match('/[0-9]/', $numberString)) {

            return $return_value;
        }
        
        $cleanNumber = preg_replace('/^[^0-9]+|[^0-9.]+$/', '', $numberString);
        
        $withoutCommas = str_replace(',', '', $cleanNumber);
        
        if (empty($withoutCommas) || !preg_match('/[0-9]/', $withoutCommas)) {
            return $return_value;
        }
        
        $parts = explode('.', $withoutCommas);
        
        $integerPart = $parts[0];
        
        if (isset($parts[1])) {
            $decimalPart = substr($parts[1], 0, $decimals);
            $decimalPart = str_pad($decimalPart, $decimals, '0');
        } else {
            $decimalPart = str_repeat('0', $decimals);
        }
        
        if (empty($integerPart) && preg_match('/^0*$/', $decimalPart)) {
            return $return_value;
        }

        return floatval($integerPart . ($decimals > 0 ? '.' . $decimalPart : ''));
    }

    private function CountDecimals($property_value){
        $parts = explode('.', $property_value);

        if (count($parts) === 2) {
            $decimals = strlen($parts[1]);
        } else {
            $decimals = 0;
        }
        return $decimals;
    }

}
