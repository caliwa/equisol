<div class="p-6" 
    x-data="{
        columns: {{ Js::from($table_columns) }},
        rows: {{ Js::from($rows_data) }},
        nextColumnId: {{ count($table_columns) > 0 ? max(array_column($table_columns, 'id')) + 1 : 1 }},
        tableKey: 0, // Key para forzar re-render

        modalDichotomicHeading: null,
        modalDichotomicMessage: null,
        deleteContext: null,

        init() {
            this.$wire.set('table_columns', this.columns);
            this.$wire.set('rows_data', this.rows);
        },

        syncWithLivewire(rowIndex, columnName, value) {
            {{-- this.$wire.set(`rows_data.${rowIndex}.${columnName}`, value); --}}
        },

        addRow() {
            const newRow = {
                _alpineId: Date.now() + Math.random()
            };
            this.columns.forEach(column => {
                newRow[column.name] = '';
            });

            this.rows = [...this.rows, newRow];
        },

        addColumn() {
            console.log(this.nextColumnId);
            const newName = `campo_${this.nextColumnId}`;
            const newColumn = {
                id: this.nextColumnId,
                name: newName,
                label: `Campo ${this.nextColumnId}`
            };
            this.columns = [...this.columns, newColumn];

            this.rows = this.rows.map(row => ({
                ...row,
                [newName]: ''
            }));
            
            this.nextColumnId++;
            this.tableKey++;
        },

        removeRow(rowIndex) {
            console.log(rowIndex);
            this.rows = this.rows.filter((_, index) => index !== rowIndex);
        },

        removeColumn(columnId) {
            const columnToRemove = this.columns.find(c => c.id === columnId);

            if (columnToRemove) {
                this.columns = this.columns.filter(c => c.id !== columnId);
                this.rows = this.rows.map(row => {
                    const newRow = { ...row };
                    delete newRow[columnToRemove.name];
                    return newRow;
                });
                
                this.tableKey++;

            }
        },

        prepareDeletion(context) {
            this.modalDichotomicHeading = context.heading;
            this.modalDichotomicMessage = context.message;
            this.deleteContext = context;
        },

        confirmDeletion() {
            if (!this.deleteContext) return;

            console.log(this.deleteContext)
            if (this.deleteContext.type === 'row') {
                this.removeRow(this.deleteContext.id);
            } else if (this.deleteContext.type === 'column') {
                this.removeColumn(this.deleteContext.id);
            }
            
            this.deleteContext = null;
        }
    }"
    x-init="init()">
    
    <flux:breadcrumbs>
        <flux:breadcrumbs.item href="#" icon="home" />
        <flux:breadcrumbs.item href="#">Maestros</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>Marítimo</flux:breadcrumbs.item>
    </flux:breadcrumbs>
    
    <flux:modal name="dichotomic-modal" class="min-w-[22rem]" :dismissible="false">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg" x-text="modalDichotomicHeading"></flux:heading>
                <flux:text class="mt-2">
                    <span x-text="modalDichotomicMessage"></span>
                </flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Cancelar</flux:button>
                </flux:modal.close>
                <flux:modal.close>
                    <flux:button @click="confirmDeletion()" variant="danger">Borrar</flux:button>
                </flux:modal.close>
            </div>
        </div>
    </flux:modal>
    
    <div class="flex gap-2 mb-4">
        <flux:button wire:click="save" color="primary">Guardar Cambios</flux:button>
        <div class="flex-1 flex justify-end gap-2">
            <button @click="addColumn()"
                class="bg-green-500 text-white px-4 py-2 rounded-sm hover:bg-green-600 transition duration-300">
                <i class="fa-solid fa-plus text-white font-bold text-sm"></i>
                Añadir Columna
            </button>
            <button @click="addRow()"
                class="bg-blue-500 text-white px-4 py-2 rounded-sm hover:bg-blue-600 transition duration-300">
                <i class="fa-solid fa-plus text-white font-bold text-sm"></i>
                Añadir Fila
            </button>
        </div>
    </div>

    <div x-show="columns.length > 0" :key="tableKey">
        <flux:table>
            <flux:table.columns>
                <template x-for="(column, colIndex) in columns" :key="`${tableKey}-col-${column.id}`">
                    <flux:table.column align="center">
                        <div class="flex items-center justify-between">
                            <span x-text="column.label"></span>
                            <template x-if="columns.length > 1">
                                <flux:modal.trigger name="dichotomic-modal">
                                    <flux:tooltip content="Borrar columna" position="top">
                                        <flux:button size="xs" 
                                            @click="prepareDeletion({
                                                type: 'column',
                                                id: column.id,
                                                heading: '¿Borrar columna?',
                                                message: `La columna '${column.label}' se eliminará permanentemente.`
                                            })"
                                            class="bg-red-500!" icon="x-mark" icon:variant="outline" />
                                    </flux:tooltip>
                                </flux:modal.trigger>
                            </template>
                        </div>
                    </flux:table.column>
                </template>
                <flux:table.column align="center">Acciones</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                <template x-for="(row, rowIndex) in rows" :key="`${tableKey}-row-${row._alpineId || rowIndex}`">
                    <flux:table.row align="center">
                        <template x-for="(column, colIndex) in columns" :key="`${tableKey}-cell-${column.id}-${row._alpineId || rowIndex}`">
                            <flux:table.cell>
                                <input
                                    :id="`${column.name}_${rowIndex}`"
                                    x-model="row[column.name]"  type="text"
                                    :placeholder="column.label"
                                    class="text-center border text-gray-900 bg-gray-50 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            </flux:table.cell>
                        </template>
                        
                        <flux:table.cell>
                            <flux:modal.trigger name="dichotomic-modal">
                                <flux:tooltip content="Borrar fila" position="top">
                                    <flux:button size="sm" 
                                        @click="prepareDeletion({
                                            type: 'row',
                                            id: rowIndex,
                                            heading: '¿Borrar fila?',
                                            message: 'La fila se eliminará permanentemente.'
                                        })"
                                        class="bg-red-500!" icon="x-mark" icon:variant="outline" />
                                </flux:tooltip>
                            </flux:modal.trigger>
                        </flux:table.cell>
                    </flux:table.row>
                </template>
            </flux:table.rows>
        </flux:table>
    </div>
</div>