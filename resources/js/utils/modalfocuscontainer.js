let currentContainerId = null;

function getTabSequence(container) {
  return Array.from(container.querySelectorAll(
    'button, input, select, textarea, a[href], [tabindex]:not([tabindex="-1"])'
  )).filter(el => !el.disabled && el.offsetParent !== null);
}

function findNextTabbableElement(container, reverse = false) {
  const elements = getTabSequence(container);
  const currentElement = document.activeElement;
  const currentIndex = elements.indexOf(currentElement);

  if (currentIndex === -1) {
    return elements[0]; // Si no se encuentra, comienza desde el principio
  }

  let nextIndex = reverse ? 
    (currentIndex - 1 + elements.length) % elements.length :
    (currentIndex + 1) % elements.length;

  return elements[nextIndex];
}

function handleTabbing(event) {
  if (event.key === 'Tab') {
    const bgValidationInput = document.getElementById('bg-validation-input');
    
    // Si bg-validation-input está oculto, permite el comportamiento normal del Tab
    if (bgValidationInput && bgValidationInput.style.display !== 'none') {
      return;
    }
    event.preventDefault();
    const container = document.getElementById(currentContainerId);
    if (container) {
      const nextElement = findNextTabbableElement(container, event.shiftKey);
      nextElement.focus();
    //   console.log(`Moved focus to:`, nextElement.tagName, nextElement.id || nextElement.className);
    }
  }
}

export function focusModal(modalId) {
  if (currentContainerId) {
    document.removeEventListener('keydown', handleTabbing);
  }

  currentContainerId = modalId;
  const container = document.getElementById(modalId);
  
  if (container) {
    document.addEventListener('keydown', handleTabbing);

    // const firstTabbableElement = getTabSequence(container)[0];
    // if (firstTabbableElement) {
    // //   console.log("Focusing first element:", firstTabbableElement.tagName, firstTabbableElement.id || firstTabbableElement.className);
    //   firstTabbableElement.focus({ preventScroll: true });
    // }
  }
}

export function clearModalFocus() {
//   console.log("Clearing modal focus");
  if (currentContainerId) {
    document.removeEventListener('keydown', handleTabbing);
  }
  currentContainerId = null;
  document.body.focus();
//   console.log("Modal focus cleared");
}

