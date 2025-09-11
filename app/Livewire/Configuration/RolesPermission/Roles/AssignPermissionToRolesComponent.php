<?php

namespace App\Livewire\Configuration\RolesPermission\Roles;

use Livewire\Component;
use Livewire\Attributes\On;
use Flux\Flux;
use Livewire\Attributes\Isolate;
use Livewire\Attributes\Validate;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Livewire\Traits\CloseModalClickTrait;
use App\Livewire\Traits\AdapterLivewireExceptionTrait;

#[Isolate]
class AssignPermissionToRolesComponent extends Component
{
    use AdapterLivewireExceptionTrait,
        CloseModalClickTrait;

    public $isVisibleAssignPermissionModal = false;

    #[Validate('required')]
    public $name = '';

    public $roleId;

    public $permissions;

    public $selectedRole;

    public $AdminGlobal;

    public $selectedPermissions = [];

    #[On('isVisibleAssignPermissionModal')]
    public function setModalVariable($value){
        $this->ResetModalVariables();
        $this->isVisibleAssignPermissionModal = $value;
    }

    #[On('mount-assign-permission-to-roles-modal')]
    public function mount_artificially($dict){
        $this->zIndexModal = $dict['zIndexModal'];


        if(!(Permission::exists())){
            $this->dispatch('confirm-validation-modal', 'No hay permisos registrados para asignarlos al rol seleccionado.');
            $this->isVisibleAssignPermissionModal = false;
            $this->dispatch('escape-enabled');
        }

        $currentRole = strtolower($dict['selectedRole']['name']);

        $bestMatchAdmin = null;

        if (!str_starts_with($currentRole, 'admin-')) {
            $adminRoles = Role::where('name', 'like', 'admin-%')->pluck('name');
            
            $maxMatches = 0;
            
            foreach ($adminRoles as $adminRole) {
                $adminBase = str_replace('admin-', '', $adminRole);
                $adminParts = explode('_', $adminBase);
                $currentParts = explode('_', $currentRole);
                
                // Contar cuántas partes coinciden
                $matches = count(array_intersect($adminParts, $currentParts));
                
                if ($matches > $maxMatches) {
                    $maxMatches = $matches;
                    $bestMatchAdmin = $adminRole;
                }
            }
            
            if ($bestMatchAdmin && $maxMatches > 0) {

            }else{
                $this->dispatch('confirm-validation-modal', 'No hay un rol tipo admin para ' . $currentRole . '.');
                $this->isVisibleAssignPermissionModal = false;
                $this->dispatch('escape-enabled');
                return;
            }
        }else{
            $this->AdminGlobal = $currentRole;
            // dd($this->AdminGlobal);
        }

        if (is_null($bestMatchAdmin)) {
            $this->permissions = Permission::orderBy('name')->get();
        }else{
            $role = Role::findByName($bestMatchAdmin);
            $this->permissions = $role->permissions;
        }

        $this->roleId = $dict['selectedRole'];
        $this->selectedRole = $dict['selectedRole'];
        $this->selectedPermissions = $dict['selectedPermissions'];

        $role = Role::where('id', $this->roleId['id'])->first();
        $this->name = $role->name;
        $this->isVisibleAssignPermissionModal = true;
        $this->dispatch('escape-enabled');
    }

    public function updateRolePermissions()
    {
        $this->selectedPermissions = array_map('intval', $this->selectedPermissions);
        
        if ($this->selectedRole) {
            $role = Role::findOrFail($this->roleId['id']);
            $role->syncPermissions($this->selectedPermissions);
            
            if (!is_null($this->AdminGlobal)) {
                $baseRoleName = str_replace('admin-', '', $this->AdminGlobal);
                
                $relatedRoles = Role::where('name', 'like', '%' . $baseRoleName . '%')
                                ->where('name', '!=', $this->AdminGlobal)
                                ->get();
                
                foreach ($relatedRoles as $relatedRole) {
                    // Obtener permisos actuales del rol relacionado
                    $currentPermissions = $relatedRole->permissions->pluck('id')->toArray();
                    
                    // Quitar permisos que NO están en selectedPermissions
                    $permissionsToKeep = array_intersect($currentPermissions, $this->selectedPermissions);
                    
                    // Sincronizar solo los permisos que deben mantenerse
                    $relatedRole->syncPermissions($permissionsToKeep);
                }
            }
        }
        
        Flux::toast('Permisos actualizados para el rol seleccionado');
        $this->dispatch('MediatorSetModalFalse', 'isVisibleAssignPermissionModal');
        $this->dispatch('escape-enabled');
        $this->dispatch('set-refresh-index-roles-component');
    }

    public function ResetModalVariables(){
        $this->resetErrorBag();
        $this->reset(array_keys($this->all()));
    }

    public function render()
    {
        return view('livewire.configuration.roles-permission.roles.assign-permission-to-roles-component', [
            'permissions' => $this->permissions
        ]);
    }
}
