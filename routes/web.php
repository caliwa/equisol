<?php

use App\Livewire\PricingCalculator;
use App\Livewire\Auth\LoginComponent;
use Illuminate\Support\Facades\Route;
use App\Livewire\FI\MediatorFIComponent;
use App\Livewire\Calculation\Bills\MediatorBillsComponent;
use App\Livewire\Calculation\Management\MediatorMenuComponent;
use App\Livewire\Calculation\Management\Provider\MediatorProviderComponent;

Route::get('/', LoginComponent::class)->name('login');
Route::get('/maestros', MediatorMenuComponent::class)->name('masters');
Route::get('/gastos', MediatorBillsComponent::class)->name('gastos');
Route::get('/cotizador-fi', MediatorFIComponent::class)->name('cotizador-fi');

Route::get('/test', PricingCalculator::class)->name('test'); //NO BORRAR SIRVE PARA CÁLCULO

Route::get('/proveedores/inicio', MediatorProviderComponent::class)->name('providers.index');

Route::get('/proveedores/{provider}/editar', MediatorProviderComponent::class)->name('providers.edit');
