export function initializeDriversDataTable() {
    $(document).ready(function () {
        const $table = $('#driversTable');
        const expectedColumnCount = $table.find('thead th').length;

        const hasValidRow = $table.find('tbody tr').toArray().some(row => {
            return $(row).find('td').length === expectedColumnCount;
        });

        if (hasValidRow) {
            $table.DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
                },
                responsive: true,
                autoWidth: false
            });
        }
    });
}
