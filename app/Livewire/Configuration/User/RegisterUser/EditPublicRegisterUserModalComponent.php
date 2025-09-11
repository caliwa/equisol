<?php

namespace App\Livewire\Configuration\User\RegisterUser;

use App\Models\Role;
use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;
use Livewire\WithFileUploads;
use Flux\Flux;
use Livewire\Attributes\Isolate;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Livewire\Traits\CloseModalClickTrait;
use App\Livewire\Traits\AdapterLivewireExceptionTrait;
use App\Livewire\Traits\AdapterValidateLivewireInputTrait;

#[Isolate]
class EditPublicRegisterUserModalComponent extends Component
{
    use WithFileUploads,
        AdapterValidateLivewireInputTrait,
        AdapterLivewireExceptionTrait,
        CloseModalClickTrait;

    public $isVisibleEditPublicRegisterUserModal = false;

    public $identification = '';
    #[Validate('required', message: 'El nombre completo de la persona es obligatorio')]
    public $full_name = '';
    #[Validate('required', message: 'El correo electrónico del usuario es obligatorio')]
    #[Validate('email', message: 'Debe ingresar un email válido (ejemplo@ejemplo.com/es)')] 
    public $email = '';
    #[Validate('min:8', message: 'La contraseña del usuario debe ser mínimo de 8 caracteres')]
    public $password = '';

    public $currentUserId = null;
    public $users;
    public $currentAvatar;

    #[Validate('nullable|image|mimes:jpeg,jpg,png|max:1024', message: 'Solo debe ingresar imágenes en formato .jpeg, .jpg o .png y de un tamaño máximo de 1MB')]
    public $avatar;


    #[On('isVisibleEditPublicRegisterUserModal')]
    public function setModalVariable($value){
        $this->ResetModalVariables();
        $this->isVisibleEditPublicRegisterUserModal = $value;
    }

    #[On('MountEditPublicUserModal')]
    public function mount_artificially($dict){
        $this->zIndexModal = $dict['zIndexModal'];

        // $this->zIndexModal = $dict['zIndexModal'];

        $this->currentUserId = Auth::id();

        $user = User::findOrFail($this->currentUserId);
        $this->full_name = $user->full_name;
        $this->email = $user->email;
        // $this->currentAvatar = $user->avatar;
        $this->password = '';
        $this->identification = $user->cedula;
        $this->isVisibleEditPublicRegisterUserModal = true;
        $this->dispatch('escape-enabled');
    }

    public function SaveRegisterUser()
    {
        $variables_to_validate = ['full_name', 'email', 'password'];
        try{
            $this->validateLivewireInput($variables_to_validate);
        } catch (\Exception $e) {
            $this->dispatch("confirm-validation-modal", $e->getMessage());
            $this->validateLivewireInput($variables_to_validate);
        }

        $this->updateUser();

        $this->dispatch('MediatorSetModalFalse', 'isVisibleEditPublicRegisterUserModal');
        $this->dispatch('set-refresh-index-register-user-component');
        $this->dispatch('escape-enabled');
        Flux::toast('Datos editados correctamente!');

    }

    protected function updateUser()
    {
        $user = User::findOrFail($this->currentUserId);
        $data = [
            'full_name' => $this->full_name,
            'email' => $this->email,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->avatar) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $this->handleAvatarUpload();
        }

        $user->update($data);
    }


    protected function handleAvatarUpload()
    {
        if ($this->avatar) {
            return $this->avatar->storeAs(
                'avatars',
                'avatar_' . time() . '.' . $this->avatar->getClientOriginalExtension(),
                'public'
            );
        }
        return null;
    }

    public function ResetModalVariables(){
        $this->resetErrorBag();
        $this->reset(array_keys($this->all()));
    }

    public function render()
    {
        return view('livewire.configuration.user.register-user.edit-public-register-user-modal-component');
    }
}
