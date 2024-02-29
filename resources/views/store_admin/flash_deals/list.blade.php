@include('common.store_admin.header')
<section class="content-main">
    <div class="content-header">
        <div>
            <h2 class="content-title card-title">All Flash Deals</h2>                       
        </div>
        <a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.flash-deals.create') }}" class="btn btn-primary btn-sm rounded">Create New Flash Deal</a>
    </div>
    <div class="card mb-4">
        <header class="card-header">
            <div class="row align-items-center">
                <div class="col-lg-4 col-md-6 me-auto">
                    <h4>Flash deals</h4>
                </div>
            </div>
        </header>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="flash-deals-table">
                    <input type="hidden" class="list_url" value="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.flash-deals.index') }}">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Banner</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Featured</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>      
                            <td class="text-center" colspan="8">Data not found..!</td> 
                            <td style="display: none;"></td>
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
        list_url = $('#flash-deals-table').find(".list_url").val();
        if ( $.fn.dataTable.isDataTable( '#flash-deals-table' ) )
            flash_list_table.destroy();
        flash_list_table = $('#flash-deals-table').DataTable({
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
                { "data": "deal_title" },  
                { "data": "banner_image" },
                { "data": "start_date" },
                { "data": "end_date" },
                { "data": "featured","orderable": false,"searchable":false}, 
                { "data": "status","orderable": false,"searchable":false},
                { "data": "action","orderable": false,"searchable":false},
            ]	 
        });
    });
    $(document).on("change",".flash-deal-status",function(){
        _this = $(this);
        value = (this.checked) ? 1 : 0;
        flash_deals_id = _this.closest("tr").find(".flash_deals_id").val();
        status_url = _this.closest("table").find(".status_url").val();
        type = $(this).attr("data-type");
        $.ajax({
            url: status_url,
            type: 'post',
            data: {_token: CSRF_TOKEN,flash_deals_id: flash_deals_id,value:value,type : type},
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