<?php

namespace App\Livewire\Auth;

use Flux\Flux;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Isolate;
use Livewire\Attributes\Validate;
use App\Livewire\Traits\AdapterValidateLivewireInputTrait;
use Illuminate\Support\Facades\Auth;


#[Isolate]
#[Layout('components.layouts.blank')]
class LoginComponent extends Component
{
    use AdapterValidateLivewireInputTrait;

    #[Validate('required', message:'Debes ingresar tu usuario')]
    public $user;
    #[Validate('required', message:'Debes ingresar tu contraseña')]
    public $password;

    public function mount(){
        if (Auth::check()) {
            $this->redirectRoute('menu', navigate:true);
        }
    }
    
    public function LoginButton(){
        $variables_validate = [
            "user",
            "password"
        ];

        try {
            $this->validateLivewireInput($variables_validate);
        } catch (\Illuminate\Validation\ValidationException $e) {
            foreach ($e->validator->errors()->getMessages() as $field => $messages) {
                $this->addError($field, $messages[0]);
            }
        }

        $credentials = [
            'name' => $this->user,
            'password' => $this->password
        ];

        if (Auth::attempt($credentials)) {
            session()->regenerate();
            Flux::toast('Bienvenido, ' . $this->user . '!', variant: 'success');
            $this->redirectRoute('menu', navigate:true);
            return;
        }
        $this->reset(['password']);

        Flux::toast('Credenciales inválidas', variant: 'warning');

    }

    public function render()
    {
        return view('livewire.auth.login-component');
    }
}
