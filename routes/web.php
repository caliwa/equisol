<?php

use App\Livewire\Auth\LoginComponent;
use App\Livewire\Bills\MediatorBillsComponent;
use App\Livewire\Menu\MediatorMenuComponent;
use Illuminate\Support\Facades\Route;

Route::get('/', LoginComponent::class)->name('login');
Route::get('/menu', MediatorMenuComponent::class)->name('menu');
Route::get('/gastos', MediatorBillsComponent::class)->name('gastos');
