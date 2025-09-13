<?php

namespace App\Livewire\Configuration\RolesPermission\Roles;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Flux\Flux;
use Livewire\Attributes\Isolate;
// use Spatie\Permission\Models\Role;
// use Spatie\Permission\Models\Permission;
use App\Models\Role;
use App\Models\Permission;
use App\Livewire\Traits\AdapterLivewireExceptionTrait;

#[Isolate]
class IndexRolesComponent extends Component
{
    use AdapterLivewireExceptionTrait,
        WithPagination;
    
    public $search = '';
    public $selectedPermissions = [];

    #[On('set-refresh-index-roles-component')]
    public function SetRefreshIndexRolesComponent(){
        $this->dispatch('$refresh');
    }

    public function OpenCreateRolesModal(){
        $mediator_dict = [];
        $this->dispatch('mediator-mount-open-create-roles-modal', $mediator_dict);
    }

    public function OpenEditRolesModal($permission_id){
        $mediator_dict = [
            'role_id' => $permission_id
        ];
        $this->dispatch('mediator-mount-open-edit-roles-modal', $mediator_dict);
    }

    #[On('DeleteRol')]
    public function DeleteRol($roleId)
    {
        $role = Role::find($roleId);
        $role->delete();

        Flux::modal('dichotomic-modal')->close();
        $this->dispatch('escape-enabled');
        
        Flux::toast('Rol eliminado correctamente!');
    }

    public function OpenAssignPermissionToRolesModal(Role $role){
        $this->selectedPermissions = $role->permissions->pluck('id')->toArray();

        $mediator_dict = [
            'selectedRole' => $role,
            'selectedPermissions' => $this->selectedPermissions
        ];

        $this->dispatch('mediator-mount-assign-permission-to-roles-modal', $mediator_dict);
    }


    public function DuplicatRol(Role $role)
    {
        $newRole = Role::create([
            'name' => $role->name . '-duplicado',
            'description' => $role->description,
            'guard_name' => 'web'
        ]);

        $permissions = $role->permissions->pluck('id')->toArray();
        $newRole->syncPermissions($permissions);
        
        $this->SetRefreshIndexRolesComponent();
        $this->dispatch('escape-enabled');
        Flux::toast('Rol duplicado');
    }

    public function render()
    {
        $roles = Role::with('permissions')
            ->where('name', 'like', '%' . $this->search . '%')
            ->orderBy('id', 'desc')
            ->paginate(10);

        $permissions = Permission::orderBy('name')->get();

        return view('livewire.configuration.roles-permission.roles.index-roles-component', [
            'roles' => $roles,
            'permissions' => $permissions
        ]);
    }
    
}