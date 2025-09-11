<?php

use App\Livewire\PricingCalculator;
use App\Livewire\Auth\LoginComponent;
use Illuminate\Support\Facades\Route;
use App\Livewire\FI\MediatorFIComponent;
use App\Livewire\Menu\MediatorMainMenuComponent;
use App\Livewire\Calculation\Bills\MediatorBillsComponent;
use App\Livewire\Calculation\Management\MediatorMenuComponent;
use App\Livewire\Configuration\MediatorConfigurationComponent;
use App\Livewire\Calculation\Management\Provider\MediatorProviderComponent;
use App\Livewire\Configuration\RolesPermission\Roles\MediatorRolesComponent;
use App\Livewire\Configuration\User\RegisterUser\MediatorRegisterUserComponent;
use App\Livewire\Configuration\RolesPermission\Permission\MediatorPermissionComponent;

Route::get('/', LoginComponent::class)->name('login');

Route::middleware(['auth'])->group(function () {
    Route::get('/menu-principal', MediatorMainMenuComponent::class)->name('menu');
    Route::get('/maestros', MediatorMenuComponent::class)->name('masters');
    Route::get('/gastos', MediatorBillsComponent::class)->name('gastos');
    Route::get('/cotizador-fi', MediatorFIComponent::class)->name('cotizador-fi');

    Route::get('/configuracion', MediatorConfigurationComponent::class)->name('configuration');

     Route::prefix('configuracion')->group(function () {
        Route::get('/usuarios', MediatorRegisterUserComponent::class)->name('users.index');
        Route::get('/roles', MediatorRolesComponent::class)->name('roles.index');
        // // ->middleware('permission:roles');
        // // Route::get('/roles', RoleManagement::class)->name('roles.index');
        Route::get('/permisos', MediatorPermissionComponent::class)->name('permissions.index');
    });

    Route::get('/test', PricingCalculator::class)->name('test'); //NO BORRAR SIRVE PARA CÁLCULO

    Route::get('/proveedores/inicio', MediatorProviderComponent::class)->name('providers.index');

    Route::get('/proveedores/{provider}/editar', MediatorProviderComponent::class)->name('providers.edit');

});
