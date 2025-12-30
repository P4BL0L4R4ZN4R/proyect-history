import '../alerts.js';
import { initCreateSite } from './create-site.js';
import { initEditSite } from './edit-site.js';
import { initDeleteSite } from './delete-site.js';
// import { initViewSite } from './view-site.js';

$(document).ready(() => {
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    initCreateSite();
    initEditSite();
    initDeleteSite();
    // initViewSite();
});
