<?php

namespace App\Livewire\Traits;

trait AdapterValidateLivewireInputTrait
{
    public function validateLivewireInput($variables_validate){
        $dynamicRules = [];
        foreach ($variables_validate as $variable) {
            if (isset($this->getRules()[$variable])) {
                $rules = $this->getRules()[$variable];
                if (is_array($rules)) {
                    $dynamicRules[$variable] = implode('|', $rules);
                } else {
                    $dynamicRules[$variable] = $rules;
                }
            }
        }

        if(empty($dynamicRules)){
            return;
        }

        $this->validate($dynamicRules);
    }
}