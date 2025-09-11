<?php

namespace App\Livewire\Configuration\User\RegisterUser;

use Livewire\Component;
use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Attributes\Isolate;
use Livewire\WithFileUploads;

use Flux\Flux;

use Livewire\Attributes\Validate;

use App\Livewire\Traits\AdapterLivewireExceptionTrait;
use App\Livewire\Traits\AdapterValidateLivewireInputTrait;

use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Storage;
use Exception;
use Livewire\WithPagination;

#[Isolate]
class IndexRegisterUserComponent extends Component
{
    use AdapterLivewireExceptionTrait,
        WithFileUploads,
        AdapterValidateLivewireInputTrait,
        WithPagination;
        

    public $currentUserId = null;
    public $selectedRole = null;
    public $availableRoles = [];
    public $rolePermissions = [];
    public $currentAvatar = null;
    #[Validate('required', message: 'VALIDACIÓN: Debe seleccionar un archivo')]
    #[Validate('file', message: 'VALIDACIÓN: Debe ser un archivo válido')]
    #[Validate('mimes:xlsx,xls', message: 'VALIDACIÓN: El archivo debe ser de tipo Excel (.xlsx o .xls)')]
    public $excelFile;

    public $search = '';

    #[On('set-refresh-index-register-user-component')]
    public function SetRefreshIndexRegisterUserComponent()
    {
        $this->dispatch('$refresh');
    }

    public function OpenRegisterUserModal()
    {
        $mediator_dict = [];
        $this->dispatch('mediator-mount-register-user-modal', $mediator_dict);

        $this->resetErrorBag();
    }

    public function OpenEditUserModal($id_user)
    {
        $mediator_dict = [
            'id_user' => $id_user,
        ];

        $this->dispatch('mediator-mount-edit-user-modal', $mediator_dict);

        $this->resetErrorBag();
    }

    public function SaveUploadedExcel()
    {
        $this->resetErrorBag();

        $variables_to_validate = ['excelFile'];

        try {
            $this->validateLivewireInput($variables_to_validate);
        } catch (\Exception $e) {
            $this->dispatch('confirm-validation-modal', $e->getMessage());
            $this->validateLivewireInput($variables_to_validate);
            return;
        }

        $path = $this->excelFile->storePublicly(path: 'excels');
        $filePath = storage_path('app/private/' . $path);

        // try {
        //     Excel::import(new UsersImport, $filePath);

        //     unlink($filePath);

        //     $this->dispatch('set-refresh-index-register-user-component');
        //     Flux::toast('Archivo excel procesado correctamente');
        // } catch (\Exception $e) {
        //     $this->dispatch('confirm-validation-modal', $e->getMessage());
        //     $this->dispatch('escape-enabled');
        // } finally {
        //     $this->dispatch('escape-enabled');
        // }
    }

    // public $users = [];
    public function mount()
    {
        $this->availableRoles = Role::all();
        // $this->users = $this->getUsersProperty();
    }

    // Esta es tu propiedad computada. Livewire la llamará automáticamente
    // y hará que el resultado esté disponible como $this->users
    public function getUsersProperty()
    {
        try {
            return User::with(['roles.permissions'])
                ->orWhere('name', 'like', '%' . $this->search . '%')
                ->orWhere('email', 'like', '%' . $this->search . '%')
                ->orderBy('id', 'desc')
                ->paginate(10);
        } catch (\Exception $e) {
            // Loguea el error para revisarlo en storage/logs/laravel.log
            dd('Error en getUsersProperty: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            // Opcional: ver directamente qué pasa en pantalla mientras debuggeas
            // dd($e->getMessage(), $e->getTraceAsString());

            // Retorna una colección vacía o lo que consideres apropiado
            return collect();
        }
    }


    public function OpenDeleteUserDichotomic($user_id, $username)
    {
        $message = '¿Está seguro de eliminar el usuario ' . $username . '?';

        $sub_dict = [
            'user_id' => $user_id,
        ];

        $mediator_dict = [
            'message' => $message,
            'livewire_dispatch' => 'dichotomic-to-delete-user-index-register',
            'sub_dict' =>  $sub_dict
        ];

        $this->dispatch('mediator-mount-dichotomic-asking-modal', $mediator_dict);
    }

    #[On('deleteUser')]
    public function DeleteUser($user_id)
    {
        // $user_id = $dict['user_id'];
        $user = User::findOrFail($user_id);

        // if ($user->avatar) {
        //     Storage::disk('public')->delete($user->avatar);
        // }

        $user->delete();

        Flux::modal('dichotomic-modal')->close();
        $this->dispatch('escape-enabled');

        Flux::toast('Usuario eliminado correctamente!');
    }

    // --- !! CORRECCIÓN AQUÍ !! ---
    // Simplemente pasamos la propiedad computada `$this->users` a la vista.
    // Livewire ya ha hecho el trabajo de llamar a `getUsersProperty()` por nosotros.
    public function render()
    {
        return view('livewire.configuration.user.register-user.index-register-user-component', [
            'users' => $this->getUsersProperty(),
        ]);
    }
}
