import '../../alerts.js';
import { initCreateCoordinator } from './create-coordinator.js';
import { initEditCoordinator } from './edit-coordinator.js';

document.addEventListener('DOMContentLoaded', () => {
    initCreateCoordinator();
    initEditCoordinator();
});
