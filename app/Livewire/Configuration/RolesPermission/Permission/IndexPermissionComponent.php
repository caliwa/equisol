<?php

namespace App\Livewire\Configuration\RolesPermission\Permission;

use App\Models\User;
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
use Livewire\Features\SupportFileUploads\WithFileUploads;
use App\Livewire\Traits\AdapterValidateLivewireInputTrait;

#[Isolate]
class IndexPermissionComponent extends Component
{
    use AdapterLivewireExceptionTrait,
        AdapterValidateLivewireInputTrait,
        WithPagination,
        WithFileUploads;
    
    public $search = '';
    public $permissionId;

    #[On('set-refresh-index-permission-component')]
    public function SetRefreshIndexPermissionComponent(){
        $this->dispatch('$refresh');
    }

    public function OpenCreatePermission(){
        $mediator_dict = [];
        $this->dispatch('mediator-mount-open-create-permission-modal', $mediator_dict);
    }

    public function OpenEditPermission($permission_id){
        $mediator_dict = [
            'permission_id' => $permission_id
        ];
        $this->dispatch('mediator-mount-open-edit-permission-modal', $mediator_dict);
    }



    #[On('DeletePermission')]
    public function DeletePermission($permissionId)
    {
        $permission = Permission::find($permissionId);
        $permission->delete();

        Flux::modal('dichotomic-modal')->close();
        $this->dispatch('escape-enabled');
        
        Flux::toast('Permiso eliminado correctamente!');
    }


    public function render()
    {
        $permissions = Permission::with('roles')
            ->where('name', 'like', '%' . $this->search . '%')
            ->orderBy('id', 'desc')
            ->paginate(10);

        $users = User::with(['permissions', 'roles'])
            ->orderBy('name')
            ->paginate(10);

        $roles = Role::orderBy('name')->get();


        return view('livewire.configuration.roles-permission.permission.index-permission-component', [
            'permissions' => $permissions,
            'users' => $users,
            'roles' => $roles
        ]);
    }
}