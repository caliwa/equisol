const selectedOptionIds = new Set();

export function handleChange(optionId) {
    var selectElement = document.getElementById(optionId);
    var defaultOption = selectElement.querySelector('option[selected]');
    if (defaultOption) {
        defaultOption.disabled = true;
        selectedOptionIds.add(optionId);
    }
}

export function cycleHandleChange(optionId) {
    var selectElement = document.getElementById(optionId);
    var defaultOption = selectElement.querySelector('option[selected]');

    if (defaultOption) {
        selectedOptionIds.delete(optionId);
        selectElement.value = '';
        defaultOption.disabled = false;
    }
}