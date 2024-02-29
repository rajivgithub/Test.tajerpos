@include('common.store_admin.header')
<section class="content-main">
    <div class="card mb-4">
        <header class="card-header">
            <h4>All Subscribers</h4>
        </header>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="subscriber-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Email</th>
                            <th>Date</th>
                            <th>Status</th>										
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="5">Data not found..!</td> 
                            <td style="display: none;"></td>
                            <td style="display: none;"></td>      
                            <td style="display: none;"></td>  
                            <td style="display: none;"></td>                                      
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>          
@include('common.store_admin.footer')
<script>
    $(document).ready(function() {
        list_url = $('#subscriber-table').find(".list_url").val();
        if ( $.fn.dataTable.isDataTable( '#subscriber-table' ) )
            subscriber_table.destroy();
        subscriber_table = $('#subscriber-table').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [[ 0, "desc" ]],
            "ajax": {
                "url": list_url,
                "dataType": "json",
                "type": "get",
                "data":{type: 'all'},
            },
            "columns": [
                { "data": "id" },
                { "data": "subscriber_email" },  
                { "data": "subscription_date" },
                { "data": "status","orderable": false,"searchable":false},
                { "data": "action","orderable": false,"searchable":false},
            ]	 
        });
    });
    $(document).on("change",".subscriber-status",function(){
        _this = $(this);
        status_value = (this.checked) ? 1 : 0;
        subscriber_id = _this.closest("tr").find(".subscriber_id").val();
        status_url = _this.closest("table").find(".status_url").val();
        $.ajax({
            url: status_url,
            type: 'post',
            data: {_token: CSRF_TOKEN,subscriber_id: subscriber_id,status_value:status_value},
            success: function(response){
                toastr.options =
                {
                    "closeButton" : true,
                    "progressBar" : true
                }
                toastr.success(response.message);
            }
        });
    });
    $(document).on("click",".flash-deal-delete",function() {
        if(confirm("Are you sure want to delete?"))
            return true;
        else
            return false;
    });
</script>