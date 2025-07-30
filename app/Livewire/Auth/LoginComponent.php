<?php

namespace App\Livewire\Auth;

use Flux\Flux;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Isolate;
use Livewire\Attributes\Validate;
use App\Livewire\Traits\AdapterValidateLivewireInputTrait;


#[Isolate]
#[Layout('components.layouts.blank')]
class LoginComponent extends Component
{
    use AdapterValidateLivewireInputTrait;

    #[Validate('required', message:'Debes ingresar tu usuario')]
    public $user;
    #[Validate('required', message:'Debes ingresar tu contraseña')]
    public $password;
    
    public function LoginButton(){
        $variables_validate = [
            "user",
            "password"
        ];
        $this->validateLivewireInput($variables_validate);

        Flux::toast('Bienvenido, ' . $this->user . '!', variant: 'success');

        $this->redirectRoute('menu', navigate:true);
    }

    public function render()
    {
        return view('livewire.auth.login-component');
    }
}
