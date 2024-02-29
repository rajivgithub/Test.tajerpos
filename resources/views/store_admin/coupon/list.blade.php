@include('common.store_admin.header')
<section class="content-main">
    <div class="content-header">
        <div>
            <h2 class="content-title card-title">All Coupons</h2>                       
        </div>
        <a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.coupon.create') }}" class="btn btn-primary btn-sm rounded">Add New Coupon</a>
    </div>
    <div class="card mb-4">
        <header class="card-header">
            <h4>Coupon Information</h4>
        </header>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="coupon-table">
                    <input type="hidden" class="list_url" value="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.coupon.index') }}">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Code</th>
                            <th>Type</th>
                            <th>Start Date</th>
                            <th>End Date</th>	
                            <th>Status</th>									
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>      
                            <td class="text-center" colspan="7">Data not found..!</td> 
                            <td style="display: none;"></td>
                            <td style="display: none;"></td>      
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
        list_url = $('#coupon-table').find(".list_url").val();
        if ( $.fn.dataTable.isDataTable( '#coupon-table' ) )
            coupon_table.destroy();
        coupon_table = $('#coupon-table').DataTable({
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
                { "data": "coupon_code" },  
                { "data": "coupon_type" },
                { "data": "start_up_date" },
                { "data": "expiration_date" },
                { "data": "status","orderable": false,"searchable":false},
                { "data": "action","orderable": false,"searchable":false},
            ]	 
        });
    });
    $(document).on("change",".coupon-status",function(){
        _this = $(this);
        status_value = (this.checked) ? 1 : 0;
        coupon_id = _this.closest("tr").find(".coupon_id").val();
        status_url = _this.closest("table").find(".status_url").val();
        $.ajax({
            url: status_url,
            type: 'post',
            data: {_token: CSRF_TOKEN,coupon_id: coupon_id,status_value:status_value},
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
    $(document).on("click",".coupon-delete",function() {
        if(confirm("Are you sure want to delete?"))
            return true;
        else
            return false;
    });
</script>