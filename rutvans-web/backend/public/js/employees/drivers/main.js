import '../../alerts.js';
import { initializeDriversDataTable } from './data-tables.js';
import { setupEditDriverModal } from './edit-driver.js';
import { setupCreateDriverModal } from './create-driver.js';

document.addEventListener('DOMContentLoaded', function () {
    initializeDriversDataTable();
    setupEditDriverModal();
    setupCreateDriverModal();
});
