<?php

use App\Livewire\PricingCalculator;
use App\Livewire\Auth\LoginComponent;
use Illuminate\Support\Facades\Route;
use App\Livewire\FI\MediatorFIComponent;
use App\Livewire\Menu\MediatorMenuComponent;
use App\Livewire\Bills\MediatorBillsComponent;
use App\Livewire\Menu\Provider\MediatorProviderComponent;
use App\Livewire\Menu\Provider\IndexProviderEditorComponent;

Route::get('/', LoginComponent::class)->name('login');
Route::get('/menu', MediatorMenuComponent::class)->name('masters');
Route::get('/gastos', MediatorBillsComponent::class)->name('gastos');
Route::get('/cotizador-fi', MediatorFIComponent::class)->name('cotizador-fi');

Route::get('/test', PricingCalculator::class)->name('test'); //NO BORRAR SIRVE PARA CÁLCULO

Route::get('/proveedores/inicio', MediatorProviderComponent::class)->name('providers.index');

Route::get('/proveedores/{provider}/editar', MediatorProviderComponent::class)->name('providers.edit');
