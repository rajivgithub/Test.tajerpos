@include('common.store_admin.header')
<section class="content-main">
    <div class="content-header">
        <div>
            <h2 class="content-title card-title">All Colors</h2>                       
        </div>
    </div>
	<div class="row">
	    <!-- <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">                      
                    <div class="row align-items-center">                          
                        <div class="col-md-3 col-12 me-auto mb-md-0 mb-3">
                            <h4>Colors</h4>
                        </div>
                    </div>							
                </div>
                <div class="card-body">
					<div class="table-responsive">
                        <table class="table table-hover" id="theme-color-table">
                            <input type="hidden" class="list_url" value="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.themes.index') }}">
                            <thead>
                                <tr>                                       
                                    <th>#</th>										                                     
                                    <th scope="col">Name</th>  		
                                    <th scope="col">Status</th>  
                                    <th scope="col" class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>      
                                    <td class="text-center" colspan="4">Data not found..!</td> 
                                    <td style="display: none;"></td>
                                    <td style="display: none;"></td>      
                                    <td style="display: none;"></td>                                
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
		</div> -->
		<div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">                      
                    <h4>Add New Color</h4>                            
                </div>
                <div class="card-body">
                    <form  method="POST" action="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.themes.store') }}">
                    @csrf
                        <input type="hidden" name="mode" value={{$mode}}> 
                        <input type="hidden" name="theme_id" class="theme-id" value="{{!empty($theme_details) && !empty($theme_details[0]->theme_id) ? Crypt::encrypt($theme_details[0]->theme_id) : '' }}">
                        <div class="mb-4 input-field-div">
                            <label class="form-label">Name</label>
                            <input type="text" placeholder="name" data-label = "Name" class="form-control required-field" name="color_name" value="{{!empty($theme_details) && !empty($theme_details[0]->color_name) ? $theme_details[0]->color_name : '' }}">
                            @if ($errors->has('color_name'))
                                <span class="text-danger error-message">{{ $errors->first('color_name') }}</span>
                            @endif
                            <span class="error error-message"></span>
                        </div>
                            <div class="mb-4 input-field-div">
                            <label class="form-label">Color Code</label>
                            <input type="text" placeholder="color code" data-label = "Color Code" class="form-control required-field" name="color_code" value="{{!empty($theme_details) && !empty($theme_details[0]->color_code) ? $theme_details[0]->color_code : '' }}">
                            @if ($errors->has('color_code'))
                                <span class="text-danger error-message">{{ $errors->first('color_code') }}</span>
                            @endif
                            <span class="error error-message"></span>
                        </div>
                        <div class="text-end">
                            <button class="btn btn-md rounded font-sm hover-up" id="save-theme-info">Save</button>
                        </div>
					</form>
				</div>
			</div>
		</div>	
	</div>
</section>          
@include('common.store_admin.footer')
<script>
    $(document).on("click","#save-theme-info",function() {
        check_fields = validateFields($(this));
        if(check_fields > 0)
            return false;
        else
            return true;
    });
    $(document).ready(function() {
        list_url = $('#theme-color-table').find(".list_url").val();
        if ( $.fn.dataTable.isDataTable( '#theme-color-table' ) )
            theme_list_table.destroy();
        theme_list_table = $('#theme-color-table').DataTable({
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
                { "data": "color_name" },
                { "data": "status","orderable": false,"searchable":false},
                { "data": "action","orderable": false,"searchable":false},
            ]	 
        });
    });
    $(document).on("change",".theme-status",function(){
        _this = $(this);
        status_value = (this.checked) ? 1 : 0;
        theme_id = _this.closest("tr").find(".theme_id").val();
        update_url = _this.closest("table").find(".status_url").val();
        $.ajax({
            url: update_url,
            type: 'post',
            data: {_token: CSRF_TOKEN,theme_id: theme_id,status_value:status_value},
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
    $(document).on("click",".theme-color-delete",function() {
        if(confirm("Are you sure want to delete?"))
            return true;
        else
            return false;
    });
</script>