<div class="" x-data="tableData()">
    <div class="demo-table-container">
        @CAppElement('table');
    </div>
</div>

@CAppPushScript
<script>
function tableData() {
    return {
        dataTable : null,
        async init() {
            let dataTable = await this.getDataTable();
            dataTable.DataTable().on( 'draw', function () {

            });
        },
        async getDataTable(seconds=10) {
            if(this.dataTable!=null) {
                return this.dataTable;
            }
            const intervalMs = 10;
            const maxLoop = (seconds * 1000) / intervalMs;
            return new Promise((resolve, reject) => {
                let loop = 0;
                const interval = setInterval(() => {
                    if ($('#table_country').data('cappDataTable')) {
                        clearInterval(interval);
                        this.dataTable = $('#table_country').data('cappDataTable');
                        resolve($('#table_country').data('cappDataTable'));
                    }
                    loop++;
                    if (loop >= maxLoop) {
                        clearInterval(interval);

                        reject('Time out occured on Get Data Table');
                    }
                }, 10);
            });

        }
    }
}
</script>

@CAppEndPushScript
