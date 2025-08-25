import { formatNumeral, registerCursorTracker, DefaultNumeralDelimiter } from 'cleave-zen';

window.formatNumeral = formatNumeral;
window.registerCursorTracker = registerCursorTracker;

// Almacén global para los inputs registrados
window.registeredInputs = new Map();

export function AdapterformatNumeral(selector, decimalesParam = 0, prefixParam = '', bandPositionPrefixParam = false) {
    const inputNumeral = document.getElementById(selector);
    if (!inputNumeral) {
        console.error(`No se encontró ningún elemento con el selector: ${selector}`);
        return;
    }

    const config = {
        element: inputNumeral,
        params: {
            decimales: decimalesParam,
            prefix: prefixParam,
            bandPositionPrefix: bandPositionPrefixParam,
        }
    };

    // Registrar el input con su configuración
    window.registeredInputs.set(selector, config);

    const applyFormat = () => {
        inputNumeral.value = window.formatNumeral(inputNumeral.value, {
            numeralThousandsGroupStyle: 'thousand',
            numeralPositiveOnly: true,
            numeralDecimalScale: decimalesParam,
            prefix: prefixParam,
            tailPrefix: bandPositionPrefixParam,
        });

        
    };

    inputNumeral.addEventListener('input', applyFormat);
    
    applyFormat();
    
}

export function applyFormatToAllInputs() {
    window.registeredInputs.forEach((config, selector) => {
        console.log(selector)
        applyFormatToInput(selector);
    });
}

function applyFormatToInput(selector) {

    const inputNumeral = document.getElementById(selector);

    const config = window.registeredInputs.get(selector);
    if (!config) {
        console.error(`No se encontró configuración para el selector: ${selector}`);
        return;
    }

    const { element, params } = config;

    if(inputNumeral){
        const applyReformat = () => {
            inputNumeral.value = window.formatNumeral(inputNumeral.value, {
                numeralThousandsGroupStyle: 'thousand',
                numeralPositiveOnly: true,
                numeralDecimalScale: params.decimales,
                prefix: params.prefix,
                tailPrefix: params.bandPositionPrefix,
            });
        };

        const dict_to_send = {
            input_id: selector,
            value: inputNumeral.value
        }

        Livewire.dispatch('mediatorto-maintain-inputs-cleave-js', { 
            dict: dict_to_send,
        });

        applyReformat();
    }

}


export function clearAllRegisteredInputsCleaveJS() {
    window.registeredInputs.forEach((config, selector) => {
        const input = document.getElementById(selector);
        if (input) {
            const newInput = input.cloneNode(true);
            input.parentNode.replaceChild(newInput, input);
        }
    });
    
    window.registeredInputs.clear();
}