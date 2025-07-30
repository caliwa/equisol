<?php

namespace App\Livewire\Menu;

use Flux\Flux;
use Livewire\Component;
use Livewire\Attributes\Isolate;

#[Isolate]
class IndexMenuComponent extends Component
{
    // Las propiedades públicas que recibirán los datos finales de Alpine
    public $table_columns = [];
    public $rows_data = [];

    public function mount()
    {
        // Inicializamos las columnas por defecto
        $this->table_columns = [
            ['id' => 0, 'name' => 'nd_kg_ini', 'label' => 'Kg Inicial'],
            ['id' => 1, 'name' => 'nd_kg_fin', 'label' => 'Kg Final'],
            ['id' => 2, 'name' => 'nd_porc_incremento', 'label' => 'Porcentaje'],
        ];
        
        // Inicializar con datos vacíos o de ejemplo
        $this->rows_data = [];
    }

    // Método para recibir los datos desde Alpine y guardarlos
    public function save()
    {
        // Validar y guardar datos
        Flux::toast('Datos guardados correctamente.');
    }

    public function render()
    {
        return view('livewire.menu.index-menu-component');
    }
}