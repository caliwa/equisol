<?php

namespace App\Livewire\Configuration\User\RegisterUser;

use Flux\Flux;
use App\Models\User;
use App\Models\Cargo;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Livewire\Attributes\Isolate;
use Livewire\Attributes\Validate;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Livewire\Traits\CloseModalClickTrait;
use App\Livewire\Traits\AdapterLivewireExceptionTrait;
use App\Livewire\Traits\AdapterValidateLivewireInputTrait;

#[Isolate]
class EditRegisterUserModalComponent extends Component
{
    use WithFileUploads,
        AdapterLivewireExceptionTrait,
        AdapterValidateLivewireInputTrait,
        CloseModalClickTrait;

    public $isVisibleEditRegisterUserModal = false;

    #[Validate('required', message: 'La cédula de la persona es obligatoria')]
    public $identification = '';
    #[Validate('required', message: 'El nombre de usuario es obligatorio')]
    #[Validate('min:4', message: 'El nombre de usuario debe ser mínimo de 4 caracteres')]
    public $name = '';
    #[Validate('required', message: 'El nombre completo de la persona es obligatorio')]
    public $full_name = '';
    #[Validate('required', message: 'El correo electrónico del usuario es obligatorio')]
    #[Validate('email', message: 'Debe ingresar un email válido (ejemplo@ejemplo.com/es)')]
    public $email = '';
    #[Validate('min:8', message: 'La contraseña del usuario debe ser mínimo de 8 caracteres')]
    #[Validate('required', message: 'La contraseña del usuario es obligatoria')]
    public $password = '';

    public $currentUserId = null;
    public $selectedRoles = []; // Cambiado a array para múltiples roles
    public $availableRoles = [];
    public $currentAvatar = null;

    // Propiedades para cargos
    public $selectedCargo = '';
    public $availableCargos = [];

    public $aux_name;

    #[Validate('nullable|image|mimes:jpeg,jpg,png|max:1024', message: 'Solo debe ingresar imágenes en formato .jpeg, .jpg o .png y de un tamaño máximo de 1MB')]
    public $avatar;

    #[On('isVisibleEditRegisterUserModal')]
    public function setModalVariable($value){
        $this->ResetModalVariables();
        $this->isVisibleEditRegisterUserModal = $value;
    }

    #[On('mount-edit-register-user-modal')]
    public function mount_artificially($dict){
        $this->zIndexModal = $dict['zIndexModal'];

        if(isset($dict['id_user'])){
            $this->currentUserId = $dict['id_user'];

            $user = User::findOrFail($this->currentUserId);
            $this->name = $user->name;
            $this->aux_name = $this->name;
            $this->full_name = $user->full_name;
            $this->email = $user->email;
            // $this->currentAvatar = $user->avatar;

            $this->identification = $user->cedula;
            
            // Cargar roles actuales del usuario
            $this->selectedRoles = $user->roles->pluck('id')->toArray();

            // Cargar cargo actual
            // $this->selectedCargo = $user->cargo_id;
            // if(is_null($this->selectedCargo)){
            //     $this->reset(['selectedCargo']);
            // }

            $this->password = '';

            // Cargar roles y cargos disponibles
        }

        $this->availableRoles = Role::all();
        $this->isVisibleEditRegisterUserModal = true;
        $this->dispatch('escape-enabled');
    }

    // Método para agregar/quitar roles
    public function toggleRole($roleId)
    {
        if (in_array($roleId, $this->selectedRoles)) {
            $this->selectedRoles = array_filter($this->selectedRoles, function($id) use ($roleId) {
                return $id != $roleId;
            });
        } else {
            $this->selectedRoles[] = $roleId;
        }
        
        // Reindexar el array para evitar problemas
        $this->selectedRoles = array_values($this->selectedRoles);
    }

    public function SaveRegisterUser()
    {
        $user = User::where('name', $this->name)->first();
        if ($user && ($this->aux_name != $this->name)) {
            $variables_to_validate = ['name'];
            $name_aux = '';
            try{
                $name_aux = $this->name;
                $this->reset(['name']);
                $this->validateLivewireInput($variables_to_validate);
            } catch (\Exception $e) {
                $this->dispatch('confirm-validation-modal', 'El Nombre de Usuario: ' .$name_aux. ' ya se encuentra registrado');
                $this->name = $name_aux;
                $this->addError('name', 'IGNORE');
                $this->dispatch('escape-enabled');
                return;
            }
        }

        $variables_to_validate = ['identification', 'name', 'full_name', 'email'];
        if(!$this->currentUserId){
            $variables_to_validate[] = 'password';
        }
        try{
            $this->validateLivewireInput($variables_to_validate);
        } catch (\Exception $e) {
            $this->dispatch("confirm-validation-modal", $e->getMessage());
            $this->validateLivewireInput($variables_to_validate);
        }

        if($this->currentUserId){
            $this->updateUser();
        }else{
            $this->createUser();
        }

        $this->dispatch('set-refresh-index-register-user-component');
        $this->dispatch('MediatorSetModalFalse', 'isVisibleEditRegisterUserModal');
        $this->dispatch('escape-enabled');
        Flux::toast('Usuario editado correctamente!');
    }

    protected function updateUser()
    {
        $user = User::findOrFail($this->currentUserId);
        $data = [
            'name' => $this->name,
            'full_name' => $this->full_name,
            'cedula' => $this->identification,
            'email' => $this->email,
        ];

        // Actualizar cargo si se seleccionó uno
        // if ($this->selectedCargo) {
        //     $data['cargo_id'] = intval($this->selectedCargo);
        // } else {
        //     $data['cargo_id'] = null;
        // }

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        // if ($this->avatar) {
        //     if ($user->avatar) {
        //         Storage::disk('public')->delete($user->avatar);
        //     }
        //     $data['avatar'] = $this->handleAvatarUpload();
        // }

        $user->update($data);

        // Sincronizar múltiples roles
        if (!empty($this->selectedRoles)) {
            $roleIds = array_map('intval', $this->selectedRoles);
            $user->syncRoles($roleIds);
            $this->dispatch('SetRefreshSidebarComponent');
        } else {
            // Si no hay roles seleccionados, remover todos los roles
            $user->syncRoles([]);
        }
    }

    protected function createUser()
    {
        $avatarPath = $this->handleAvatarUpload();

        $user = User::create([
            'name' => $this->name,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'avatar' => $avatarPath,
            'cedula' => $this->identification,
        ]);

        // Asignar múltiples roles
        if (!empty($this->selectedRoles)) {
            $roleIds = array_map('intval', $this->selectedRoles);
            $user->assignRole($roleIds);
            $this->dispatch('SetRefreshSidebarComponent');
        }
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
        return view('livewire.configuration.user.register-user.edit-register-user-modal-component');
    }
}