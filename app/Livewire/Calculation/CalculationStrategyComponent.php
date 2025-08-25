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

        $this->idx_variable = $dict['idx_item'] ?? null;

        $this->costItem = new CostItem();
        $this->expression = $this->costItem->formula_string ?? '';
        $this->ruleSet = $this->costItem->rules ?? ['default_value' => 0, 'rules' => []];
        $this->mode = !empty($this->costItem->rules) ? 'rules' : 'formula';

        $this->isVisibleCalculationStrategyComponent = true;
        
        $this->dispatch('escape-enabled');
    }


    public CostItem $costItem;
    public string $mode = 'formula'; // 'formula' o 'rules'

     // --- Propiedades para la creación de variables ---
    public string $newVariableName = '';
    public array $availableVariables = ['PESO', 'CIF']; // Variables base

    // --- Propiedades para el MODO FÓRMULA ---
    public string $expression = '';
    public array $testVariables = ['PESO' => 100, 'CIF' => 5000, 'OTRA_VARIABLE' => 2000];
    public $testResultFormula = null;
    public ?string $testError = null;

    // --- Propiedades para el MODO REGLAS ---
    public array $ruleSet = ['default_value' => 0, 'rules' => []];
    public array $availableOperators = ['==', '!=', '>', '>=', '<', '<='];
    public $testResultRules = null;


    public function setMode(string $mode)
    {
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
        $this->expression = trim(mb_substr($this->expression, 0, -1));
    }



    public function clearExpression()
    {
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
        } catch (\Exception $e) {
            $this->testError = "Error en la fórmula: " . $e->getMessage();
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
    }

    public function removeRule(int $ruleIndex)
    {
        unset($this->ruleSet['rules'][$ruleIndex]);
        $this->ruleSet['rules'] = array_values($this->ruleSet['rules']);
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
    }

    //======================================================================
    // MÉTODO DE GUARDADO
    //======================================================================

    public function save()
    {
        $mediator_dict = [
            'expression' => $this->expression,
            'idx_item' => $this->idx_variable
        ];
        $this->dispatch('mediator-calculation-to-index-bills', $mediator_dict);
        $this->dispatch('MediatorSetModalFalse', 'isVisibleCalculationStrategyComponent');
        return;

        $datosDeEntrada = [
            'OTRA_VARIABLE' => 2000, // Este valor es dinámico
            'PESO' => 150,
            'CIF' => 8500,
        ];
        $evaluador = new ExpressionLanguage();

        dd($this->expression);

        $resultadoFinal = $evaluador->evaluate(
            $this->expression,  // La receta de la BD
            $datosDeEntrada    // Los ingredientes de esta operación
        );

        // 3. ¡Listo!
        dd($resultadoFinal);

        return;
        if ($this->mode === 'formula') {
            $this->costItem->update([
                'formula_string' => $this->expression,
                'rules' => null, // Limpia el otro modo
            ]);
        } else {
            $this->costItem->update([
                'rules' => $this->ruleSet,
                'formula_string' => null, // Limpia el otro modo
            ]);
        }
        $this->dispatch('saved');
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
