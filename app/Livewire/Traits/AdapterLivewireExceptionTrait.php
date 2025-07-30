<?php

namespace App\Livewire\Traits;

trait AdapterLivewireExceptionTrait
{
    public $zIndexModal = 0;

    public function exception($e, $stopPropagation)
    {
        if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            $errorMessage = $e->getMessage();
            $this->dispatch('confirm-validation-modal', $errorMessage);
        } elseif ($e instanceof \PDOException) {
            $errorMessage = $e->getMessage();
            $this->dispatch('confirm-validation-modal', $errorMessage);
        } elseif ($e instanceof \Illuminate\Database\QueryException) {
            $errorMessage = $e->getMessage();
            $this->dispatch('confirm-validation-modal', $errorMessage);
        } elseif ($e instanceof \ErrorException) {
            $errorMessage = $e->getMessage();
            $this->dispatch('confirm-validation-modal', $errorMessage);
        } elseif ($e instanceof \Exception) {
            $errorMessage = $e->getMessage();
            if(!(str_starts_with(strtoupper($errorMessage), 'VALIDA')) && !str_contains($errorMessage, "IGNORE")){
                $this->dispatch('confirm-validation-modal', $errorMessage);
            }
        }
        $stopPropagation();
    

        $this->dispatch('MediatorSetModalFalse', 'isVisibleDichotomicAskingModal');
    
    }
}