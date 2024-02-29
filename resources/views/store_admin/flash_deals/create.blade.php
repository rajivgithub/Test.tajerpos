@include('common.store_admin.header')
<section class="content-main">
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header">
                @php
                    $page_title = ($mode == "add") ? "Add Flash Deal Information" : "Edit Flash Deal Information";
                    $fields_validation = !empty($flash_deals_details) && !empty($flash_deals_details[0]->banner_image) ? 'optional-field' : 'required-field';
                    $image_path = !empty($flash_deals_details) && !empty($flash_deals_details[0]->banner_image) ? $flash_deals_details[0]->banner_image : '';
                @endphp
                <h4>{{$page_title}}</h4>
            </div>
            <div class="card-body">
                <form  method="POST" action="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.flash-deals.store') }}" enctype="multipart/form-data">
                @csrf
                    <input type="hidden" name="mode" value={{$mode}}> 
                    <input type="hidden" name="flash_deals_id" class="flash_deals_id" value="{{!empty($flash_deals_details) && !empty($flash_deals_details[0]->flash_deals_id) ? Crypt::encrypt($flash_deals_details[0]->flash_deals_id) : '' }}">
                    <div class="mb-4 input-field-div">
                        <label class="form-label">Title</label>
                        <input type="text" placeholder="Type here" data-label = "Title" class="form-control required-field" name="deal_title" value="{{!empty($flash_deals_details) && !empty($flash_deals_details[0]->deal_title) ? $flash_deals_details[0]->deal_title : '' }}">
                        @if ($errors->has('deal_title'))
                            <span class="text-danger error-message">{{ $errors->first('deal_title') }}</span>
                        @endif
                        <span class="error error-message"></span>
                    </div>
                    <div class="mb-4 input-field-div">
                        <label class="form-label">Background Color </label>
                        <input type="text" placeholder="Type here" data-label = "Background Color" class="form-control" name="background_color" value="{{!empty($flash_deals_details) && !empty($flash_deals_details[0]->background_color) ? $flash_deals_details[0]->background_color : '' }}">
                        @if ($errors->has('background_color'))
                            <span class="text-danger error-message">{{ $errors->first('background_color') }}</span>
                        @endif
                        <span class="error error-message"></span>
                    </div>
                    <div class="mb-4 input-field-div">
                        <label class="form-label">Text Color</label>
                        <select class="form-select" data-label = "Text Color" name="text_color" value="{{!empty($flash_deals_details) && !empty($flash_deals_details[0]->text_color) ? $flash_deals_details[0]->text_color : '' }}">
                            <option value="">Choose Color</option>
                            <option value="white" {{!empty($flash_deals_details) && !empty($flash_deals_details[0]->text_color) && ($flash_deals_details[0]->text_color == 'white') ? "selected" : '' }}>White</option>
                            <option value="dark" {{!empty($flash_deals_details) && !empty($flash_deals_details[0]->text_color) && ($flash_deals_details[0]->text_color == 'dark') ? "selected" : '' }}>Dark</option>
                        </select>
                        @if ($errors->has('text_color'))
                            <span class="text-danger error-message">{{ $errors->first('text_color') }}</span>
                        @endif
                        <span class="error error-message"></span>
                    </div>
                    <div class="mb-4 input-field-div">
                        <label class="form-label">Banner (1920x500)</label>
                        <div class="input-upload">                                    
                            <input class="form-control  {{$fields_validation}}  image-field" data-type="image" type="file" data-label = "Banner" name="banner_image">
                        </div>
                        <div class="file-preview row">
                            <div class="d-flex mt-2 ms-2 file-preview-item">
                                <div class="align-items-center thumb">
                                    <img src="{{ $image_path }}" class="img-fit image-preview" alt="Item">
                                </div>
                                <div class="remove"><button class="btn btn-sm btn-link remove-attachment" type="button"><i class="fa fa-close"></i></button></div>
                            </div>
                        </div>
                        @if ($errors->has('banner_image'))
                            <span class="text-danger error-message">{{ $errors->first('banner_image') }}</span>
                        @endif
                        <span class="error error-message"></span>
                    </div>
                    <div class="mb-4">
						<div class="row">
						    <div class="col-6 input-field-div">
                                <label class="form-label">Start Date</label>
                                <input type="date" placeholder="Type here" class="form-control required-field validity-date" data-label = "Start Date" name="start_date" value="{{!empty($flash_deals_details) && !empty($flash_deals_details[0]->start_date) ? date('Y-m-d', strtotime(trim($flash_deals_details[0]->start_date))) : '' }}">
                                @if ($errors->has('start_date'))
                                    <span class="text-danger error-message">{{ $errors->first('start_date') }}</span>
                                @endif
                                <span class="error error-message"></span>
							</div>
							<div class="col-6 input-field-div">
                                <label class="form-label">End Date</label>
								<input type="date" placeholder="Type here" class="form-control required-field validity-date" data-label = "End Date" name="end_date" data-date-type="{{!empty($flash_deals_details) && !empty($flash_deals_details[0]->end_date) ? date('Y-m-d', strtotime(trim($flash_deals_details[0]->end_date))) : '' }}" value="{{!empty($flash_deals_details) && !empty($flash_deals_details[0]->end_date) ? date('Y-m-d', strtotime(trim($flash_deals_details[0]->end_date))) : '' }}">
                                @if ($errors->has('end_date'))
                                    <span class="text-danger error-message">{{ $errors->first('end_date') }}</span>
                                @endif
                                <span class="error error-message"></span>
							</div>
						</div>
                    </div>
					<div class="mt-3">
                        <button class="btn btn-md rounded font-sm hover-up" id="save-flash-info">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@include('common.store_admin.footer')
<script>
    $(document).on("click","#save-flash-info",function() {
        check_fields = validateFields($(this));
        if(check_fields > 0)
            return false;
        else
            return true;
    });
    //Minimum validation for Date field
    var today = new Date();
    var month = today.getMonth()+1;
    var date = today.getDate();
    var min_date = today.getFullYear() + '-' + (month<10 ? '0' : '') + month + '-' + (date <10 ? '0' : '') + date;
    $(".validity-date").attr("min",min_date);
</script>