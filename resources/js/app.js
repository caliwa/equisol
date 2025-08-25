import './bootstrap';

import {handleChange} from './utils/handlechange';
import {focusModal, clearModalFocus} from './utils/modalfocuscontainer';

document.addEventListener('livewire:navigated', () => {
});

Livewire.hook('request', ({ fail }) => {
    fail(({ status, preventDefault }) => {
        if (status === 419) {
            preventDefault();
            location.reload();
            // confirm('Your custom page expiration behavior...')
        }
    })
})

Livewire.hook('component.init', ({ component, cleanup }) => {
    (function() {
        window.history.pushState(null, "", window.location.href);
        window.onpopstate = function() {
          window.history.pushState(null, "", window.location.href);
        };
    })();


    window.handleChange = handleChange;
    window.focusModal = focusModal;
    window.clearModalFocus = clearModalFocus;
});
