<?php

namespace App\Livewire\Calculation;

use Livewire\Component;
use Livewire\Attributes\Isolate;
use Livewire\Attributes\On;

use App\Livewire\Traits\AdapterLivewireExceptionTrait;
use App\Livewire\Traits\AdapterValidateLivewireInputTrait;
use App\Livewire\Traits\CloseModalClickTrait;

use App\Models\CostItem; // Asegúrate que la ruta a tu modelo sea correcta
use App\Services\RuleEngineService;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Illuminate\Support\Str;

#[Isolate]
class CalculationStrategyComponent extends Component
{
    use AdapterLivewireExceptionTrait,
        AdapterValidateLivewireInputTrait,
        CloseModalClickTrait;

    public $isVisibleCalculationStrategyComponent = false;

    public $idx_variable;

    #[On('isVisibleCalculationStrategyComponent')]
    public function setModalVariable($value)
    {
        $this->ResetModalVariables();
        $this->isVisibleCalculationStrategyComponent = $value;
    }

    #[On('mount-calculation-strategy-modal')]
    public function mount_artificially($dict){
        $this->zIndexModal = $dict['zIndexModal'];

        $this->idx_variable = $dict['idx_item'];

        $logic = json_decode($dict['logic'], true);

        if (!empty($logic)) {
            $this->mode = $logic['type'];

            if ($this->mode == 'formula') {
                $this->expression = $logic['expression'] ?? '';
                $this->addRule();
            } elseif ($this->mode == 'rules') {
                $this->ruleSet = $logic['expression'] ?? ['default_value' => 0, 'rules' => []];
            }
        } else {
            $this->mode = 'formula';
            $this->expression = '';
            $this->addRule();
        }

        $this->isVisibleCalculationStrategyComponent = true;
        
        $this->dispatch('escape-enabled');
    }


    public CostItem $costItem;
    public string $mode = 'formula'; // 'formula' o 'rules'

     // --- Propiedades para la creación de variables ---
    public string $newVariableName = '';
    public array $availableVariables = ['PESO', 'CIF', 'ARANCEL']; // Variables base

    // --- Propiedades para el MODO FÓRMULA ---
    public string $expression = '';
    public bool $isUpdatedExpression = true;

    public array $testVariables = ['PESO' => 100, 'CIF' => 5000, 'OTRA_VARIABLE' => 2000];
    public $testResultFormula = null;
    public ?string $testError = null;

    // --- Propiedades para el MODO REGLAS ---
    public array $ruleSet = ['default_value' => 0, 'rules' => []];
    public array $availableOperators = ['==', '!=', '>', '>=', '<', '<='];
    public $testResultRules = null;


    public function setMode(string $mode)
    {
        if($mode == 'rules'){
            $this->isUpdatedExpression = false;
        }
        $this->mode = $mode;
    }

     public function addVariable()
    {
        // 1. Limpia y formatea el nombre de la variable
        $cleanName = strtoupper(Str::slug($this->newVariableName, '_'));

        // 2. Valida que no esté vacío y no exista ya
        if (!empty($cleanName) && !in_array($cleanName, $this->availableVariables)) {
            // 3. Añade la nueva variable a la lista
            $this->availableVariables[] = $cleanName;
            // 4. Inicializa su valor de prueba
            $this->testVariables[$cleanName] = 0;
            // 5. Limpia el input
            $this->newVariableName = '';
        }
    }

    //======================================================================
    // MÉTODOS PARA EL MODO FÓRMULA (ExpressionLanguage)
    //======================================================================

    public function addToken(string $token)
    {
        $this->isUpdatedExpression = true;
        // Lista de tokens que SIEMPRE deben llevar espacios
        $tokensWithSpaces = ['+', '-', '*', '/', ',', 'PESO', 'CIF', 'OTRA_VARIABLE'];

        if (in_array($token, $tokensWithSpaces)) {
            // Si es un operador o variable, añade espacios
            $this->expression .= ' ' . $token . ' ';
        } else {
            // Si es un número, punto, paréntesis o función, lo añade directamente
            $this->expression .= $token;
        }

        // Limpia posibles dobles espacios para mantenerlo legible
        $this->expression = trim(str_replace('  ', ' ', $this->expression));
    }

    public function backspace()
    {
        $this->reset(['isUpdatedExpression']);
        $this->expression = trim(mb_substr($this->expression, 0, -1));
    }



    public function clearExpression()
    {
        $this->reset(['isUpdatedExpression']);
        $this->expression = '';
        $this->testResultFormula = null;
        $this->testError = null;
    }

    public function testExpression()
    {
        $this->testResultFormula = null;
        $this->testError = null;

        $el = new ExpressionLanguage();
        try {
            $this->testResultFormula = $el->evaluate($this->expression, $this->testVariables);
            $this->isUpdatedExpression = false;
        } catch (\Exception $e) {
            $this->testError = "Error en la fórmula: " . $e->getMessage();
            $this->reset(['isUpdatedExpression']);
        }
    }

    //======================================================================
    // MÉTODOS PARA EL MODO REGLAS (JSON Engine)
    //======================================================================

    public function addRule()
    {
        $this->ruleSet['rules'][] = [
            'result' => 0,
            'conditions' => [['variable' => 'PESO', 'operator' => '>=', 'value' => 0]]
        ];
        $this->reset(['isUpdatedExpression']);
    }

    public function removeRule(int $ruleIndex)
    {
        unset($this->ruleSet['rules'][$ruleIndex]);
        $this->ruleSet['rules'] = array_values($this->ruleSet['rules']);
        $this->reset(['isUpdatedExpression']);
    }

    public function addCondition(int $ruleIndex)
    {
        $this->ruleSet['rules'][$ruleIndex]['conditions'][] = [
            'variable' => 'PESO', 'operator' => '<=', 'value' => 0
        ];
    }

    public function removeCondition(int $ruleIndex, int $conditionIndex)
    {
        unset($this->ruleSet['rules'][$ruleIndex]['conditions'][$conditionIndex]);
        $this->ruleSet['rules'][$ruleIndex]['conditions'] = array_values($this->ruleSet['rules'][$ruleIndex]['conditions']);
    }
    
    public function testRules(RuleEngineService $engine)
    {
        $this->testResultRules = $engine->process($this->ruleSet, $this->testVariables);
        $this->isUpdatedExpression = false;
    }

    //======================================================================
    // MÉTODO DE GUARDADO
    //======================================================================

    public function save()
    {
        if($this->isUpdatedExpression){
            $this->dispatch('confirm-validation-modal', 'Por favor, pruebe la fórmula antes de guardar. Si la ha modificado, pruebe y vuelva a intentarlo.');
            return;
        }

        // $datosDeEntrada = [
        //     'OTRA_VARIABLE' => 2000, // Este valor es dinámico
        //     'PESO' => 150,
        //     'CIF' => 8500,
        // ];
        // $evaluador = new ExpressionLanguage();

        // $resultadoFinal = $evaluador->evaluate(
        //     $this->expression,  // La receta de la BD
        //     $datosDeEntrada    // Los ingredientes de esta operación
        // );

        $mediator_dict = [
            'idx_item' => $this->idx_variable
        ];

        // 3. ¡Listo!
        if ($this->mode == 'formula') {
            // Construye el objeto para el tipo "formula"
            $logicToSave = [
                'type' => 'formula',
                'expression' => $this->expression
            ];
            // Opcional: Extraer y añadir las variables
            // preg_match_all('/[A-Z_]+/', $this->expression, $matches);
            // $logicToSave['required_variables'] = ...;

        } else { // Asumimos que es 'rules'
            // Construye el objeto para el tipo "rules"
            $logicToSave = [
                'type' => 'rules',
                'expression' => $this->ruleSet
            ];
        }

        $mediator_dict['logic'] = json_encode($logicToSave);

        $this->dispatch('escape-enabled');

        $this->dispatch('mediator-calculation-to-index-bills', $mediator_dict);
        $this->dispatch('MediatorSetModalFalse', 'isVisibleCalculationStrategyComponent');
    }

    public function ResetModalVariables(){
        $this->resetErrorBag();
        $this->reset(array_keys($this->all()));
    }

    public function render()
    {
        return view('livewire.calculation.calculation-strategy-component');
    }
}
