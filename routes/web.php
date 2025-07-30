<?php

use App\Livewire\Auth\LoginComponent;
use App\Livewire\Menu\MediatorMenuComponent;
use Illuminate\Support\Facades\Route;

Route::get('/', LoginComponent::class)->name('login');
Route::get('/menu', MediatorMenuComponent::class)->name('menu');
