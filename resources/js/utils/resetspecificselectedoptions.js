export function resetSpecificSelectedOptions(ids) {
    console.log(ids)
    ids.forEach(id => {
        const selectElement = document.getElementById(id);
        const defaultOption = selectElement.querySelector('option[selected]');
        
        if (defaultOption) {
            defaultOption.disabled = false;
        }
        
        selectedOptionIds.delete(id);
    });
}