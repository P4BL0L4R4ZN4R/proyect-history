export function initializeCashiersDataTable() {
    $(document).ready(function () {
        $('#cashiersTable').DataTable({
           language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
                    },
            responsive: true,
            autoWidth: false
        });
    });
}
