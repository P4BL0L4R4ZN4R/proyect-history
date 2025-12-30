// main.js

import { initEditCompany } from './edit-company.js';
import { initViewCompany } from './view-company.js';
import { initDeleteCompany } from './delete-company.js';
import { initCreateCompany } from './create-company.js';
import { initEditAdmin } from './edit-admin/edit-admin.js';

$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    initViewCompany();
    initEditCompany();
    initDeleteCompany();
    initCreateCompany();
    initEditAdmin();
});
