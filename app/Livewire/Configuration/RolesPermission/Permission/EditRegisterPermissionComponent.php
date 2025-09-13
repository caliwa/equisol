<?php

namespace App\Livewire\Configuration\RolesPermission\Permission;

use Flux\Flux;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Isolate;
use Livewire\Attributes\Validate;
// use Spatie\Permission\Models\Permission;
use App\Models\Permission;
use App\Livewire\Traits\CloseModalClickTrait;
use App\Livewire\Traits\AdapterLivewireExceptionTrait;
use App\Livewire\Traits\AdapterValidateLivewireInputTrait;

#[Isolate]
class EditRegisterPermissionComponent extends Component
{
    use AdapterLivewireExceptionTrait,
        AdapterValidateLivewireInputTrait,
        CloseModalClickTrait;
    
    public $isVisibleEditRegisterPermissionModal = false;

    #[Validate('required', message: 'El nombre del permiso es obligatorio')]
    public $name = '';
    
    #[Validate('required', message: 'La descripción del permiso es obligatorio')]
    public $description = '';

    public $permissionId;

    public $aux_name;

    #[On('isVisibleEditRegisterPermissionModal')]
    public function setModalVariable($value){
        $this->ResetModalVariables();
        $this->isVisibleEditRegisterPermissionModal = $value;
    }

    #[On('mount-open-edit-register-permission-modal')]
    public function mount_artificially($dict){
        $this->zIndexModal = $dict['zIndexModal'];

        if(isset($dict['permission_id'])){
            $this->permissionId = $dict['permission_id'];
            $permission = Permission::where('id', $this->permissionId)->first();
            $this->name = $permission->name;
            $this->aux_name = $this->name;
            $this->description = $permission->description;
        }

        $this->isVisibleEditRegisterPermissionModal = true;
        $this->dispatch('escape-enabled');
    }

    public function update()
    {
        $permission = Permission::where('name', $this->name)->first();
        if ($permission && ($this->aux_name != $this->name)) {
            $variables_to_validate = ['name'];
            $name_aux = '';
            try{
                $name_aux = $this->name;
                $this->reset(['name']);
                $this->validateLivewireInput($variables_to_validate);
            } catch (\Exception $e) {
                $this->dispatch('confirm-validation-modal', 'El permiso: ' .$name_aux. ' ya se encuentra registrado');
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

        if($this->permissionId){
            $permission = Permission::find($this->permissionId);
            $permission->update([
                'name' => $this->name,
                'description' => $this->description
            ]);
            Flux::toast('Permiso editado');
        }else{

            Permission::create([
                'name' => $this->name,
                'description' => $this->description,
            ]);
            Flux::toast('Registro creado');
        }

        $this->dispatch('set-refresh-index-permission-component');
        $this->dispatch('escape-enabled');
        $this->dispatch('MediatorSetModalFalse', 'isVisibleEditRegisterPermissionModal');
    }

    public function ResetModalVariables(){
        $this->resetErrorBag();
        $this->reset(array_keys($this->all()));
    }

    public function render()
    {
        return view('livewire.configuration.roles-permission.permission.edit-register-permission-component');
    }
}
