<?php

namespace App\Livewire\Validation;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Isolate;
use App\Livewire\Traits\CloseModalClickTrait;

#[Isolate]
class DichotomicAskingModalComponent extends Component
{
    use CloseModalClickTrait;

    public $isVisibleDichotomicAskingModal;

    public $message = '';
    public $livewire_dispatch = '';
    public $dict = [];

    #[On('isVisibleDichotomicAskingModal')]
    public function setModalVariable($value){
        $this->ResetModalVariables();
        $this->isVisibleDichotomicAskingModal = $value;
    }

    #[On('mount-dichotomic-asking-modal')]
    public function mount_artificially($dict){
        $this->dict = $dict;
        $this->message = $this->dict["message"];
        $this->livewire_dispatch = $this->dict["livewire_dispatch"];
        $this->isVisibleDichotomicAskingModal = true;
        $this->dispatch('escape-enabled');
    }

    public function DoDispatch(){
        if(isset($this->dict["sub_dict"])){
            $this->dispatch($this->livewire_dispatch, $this->dict["sub_dict"]);
        }else{
            $this->dispatch($this->livewire_dispatch);
        }
    }

    public function ResetModalVariables(){
        $this->reset(array_keys($this->all()));
    }

    public function render()
    {
        return view('livewire.validation.dichotomic-asking-modal-component');
    }
}
