<?php

namespace App\Livewire\Configuration\RolesPermission\Roles;

use Livewire\Component;
use Livewire\Attributes\On;
use Flux\Flux;
use Livewire\Attributes\Isolate;
use Livewire\Attributes\Validate;
// use Spatie\Permission\Models\Role;
use App\Models\Role;
use App\Livewire\Traits\CloseModalClickTrait;
use App\Livewire\Traits\AdapterLivewireExceptionTrait;
use App\Livewire\Traits\AdapterValidateLivewireInputTrait;

#[Isolate]
class EditRegisterRolesComponent extends Component
{
    use AdapterLivewireExceptionTrait,
        AdapterValidateLivewireInputTrait,
        CloseModalClickTrait;

    public $isVisibleEditRegisterRolesModal = false;

    #[Validate('required', message: 'El nombre del permiso es obligatorio')]
    public $name = '';
    
    #[Validate('required', message: 'La descripción del permiso es obligatorio')]
    public $description = '';

    public $roleId;

    public $aux_name;

    #[On('isVisibleEditRegisterRolesModal')]
    public function setModalVariable($value){
        $this->ResetModalVariables();
        $this->isVisibleEditRegisterRolesModal = $value;
    }

    #[On('mount-open-edit-register-roles-modal')]
    public function mount_artificially($dict){
        $this->zIndexModal = $dict['zIndexModal'];

        if(isset($dict['role_id'])){
            $this->roleId = $dict['role_id'];
            $role = Role::where('id', $this->roleId)->first();
            $this->name = $role->name;
            $this->aux_name = $this->name;
            $this->description = $role->description;
        }
        $this->isVisibleEditRegisterRolesModal = true;
        $this->dispatch('escape-enabled');
    }

    public function update()
    {
        $rol = Role::where('name', $this->name)->first();
        if ($rol && ($this->aux_name != $this->name)) {
            $variables_to_validate = ['name'];
            $name_aux = '';
            try{
                $name_aux = $this->name;
                $this->reset(['name']);
                $this->validateLivewireInput($variables_to_validate);
            } catch (\Exception $e) {
                $this->dispatch('confirm-validation-modal', 'El rol: ' .$name_aux. ' ya se encuentra registrado');
                $this->name = $name_aux;
                $this->addError('name', 'IGNORE');
                $this->dispatch('escape-enabled');
                return;
            }
        }
        $variables_to_validate = ['name', 'description'];
        try{
            $this->validateLivewireInput($variables_to_validate);
        } catch (\Exception $e) {
            $this->dispatch("confirm-validation-modal", $e->getMessage());
            $this->validateLivewireInput($variables_to_validate);
        }

        if($this->roleId){
            
            $role = Role::find($this->roleId);
            $role->update([
                'name' => $this->name,
                'description' => $this->description
            ]);
        }else{
            Role::create([
                'name' => $this->name,
                'description' => $this->description,
                'guard_name' => 'web'
            ]);
        }

        Flux::toast('Rol creado');

        Flux::toast('Rol editado');
        $this->dispatch('set-refresh-index-roles-component');
        $this->dispatch('escape-enabled');
        $this->dispatch('MediatorSetModalFalse', 'isVisibleEditRegisterRolesModal');

    }

    public function ResetModalVariables(){
        $this->resetErrorBag();
        $this->reset(array_keys($this->all()));
    }

    public function render()
    {
        return view('livewire.configuration.roles-permission.roles.edit-register-roles-component');
    }
}
