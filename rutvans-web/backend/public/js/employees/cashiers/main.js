import '../../alerts.js';
import { initializeCashiersDataTable } from './data-tables.js';
import { setupEditCashierModal } from './edit-cashier.js';
import { setupCreateCashierModal } from './create-cashiers.js';

document.addEventListener('DOMContentLoaded', function () {
    initializeCashiersDataTable();
    setupEditCashierModal();
    setupCreateCashierModal();
});
