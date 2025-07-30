import './bootstrap';

document.addEventListener('livewire:navigated', () => {
});

Livewire.hook('component.init', ({ component, cleanup }) => {
    console.log(component.name);
});
