@include('common.store_admin.header')
<section class="content-main">
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header">
                <h4>Send Newsletter</h4>
            </div>
            <div class="card-body">
            <form  method="POST" action="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.newsletters.store') }}">
            @csrf
                <input type="hidden" name="mode" value={{$mode}}> 
                <input type="hidden" name="newsletter_id" class="newsletter-id" value="{{!empty($newsletters_details) && !empty($newsletters_details[0]->newsletter_id) ? Crypt::encrypt($newsletters_details[0]->newsletter_id) : '' }}">
                    <div class="mb-4 input-field-div">
                        <label class="form-label">Emails (Users)</label>
                        <select class="form-select required-field" data-label = "Emails (Users)" name="user_id">
                            <option value="">--Select Users--</option> 
                            @if(isset($customer_details) && !empty($customer_details))
                                @foreach ($customer_details as $customer)
                                    <option value="{{ $customer->customer_id }}" {{!empty($newsletters_details) && !empty($newsletters_details[0]->user_id) && ($newsletters_details[0]->user_id == $customer->customer_id) ? "selected" : '' }}>{{ $customer->customer_email }}</option> 
                                @endforeach
                            @endif
                        </select>
                        @if ($errors->has('user_id'))
                            <span class="text-danger error-message">{{ $errors->first('user_id') }}</span>
                        @endif
                        <span class="error error-message"></span>
                    </div>
                    <div class="mb-4 input-field-div">
                        <label class="form-label">Emails (Subscribers) </label>
                        <select class="form-select required-field" data-label = "Emails (Subscribers)" name="subscriber_id">
                            <option value="">--Select Subscribers--</option> 
                            @if(isset($subscriber_details) && !empty($subscriber_details))
                                @foreach ($subscriber_details as $subscriber)
                                    <option value="{{ $subscriber->subscriber_id }}" {{!empty($newsletters_details) && !empty($newsletters_details[0]->subscriber_id) && ($newsletters_details[0]->subscriber_id == $subscriber->subscriber_id) ? "selected" : '' }}>{{ $subscriber->subscriber_email }}</option> 
                                @endforeach
                            @endif
                        </select>
                        @if ($errors->has('subscriber_id'))
                            <span class="text-danger error-message">{{ $errors->first('subscriber_id') }}</span>
                        @endif
                        <span class="error error-message"></span>
                    </div>
                    <div class="mb-4 input-field-div">
                        <label class="form-label">Newsletter subject</label>
                        <input type="text" placeholder="Type here" class="form-control required-field" data-label = "Newsletter subject" name="subject" value="{{!empty($newsletters_details) && !empty($newsletters_details[0]->subject) ? $newsletters_details[0]->subject : '' }}">
                        @if ($errors->has('subject'))
                            <span class="text-danger error-message">{{ $errors->first('subject') }}</span>
                        @endif
                        <span class="error error-message"></span>
                    </div>
                    <div class="mb-4 input-field-div">
                        <label class="form-label">Newsletter content</label>                                                                   
                        <textarea class="form-control required-field" placeholder="Type here" data-label = "Newsletter content" name="content">{{!empty($newsletters_details) && !empty($newsletters_details[0]->content) ? $newsletters_details[0]->content : '' }}</textarea>
                        @if ($errors->has('content'))
                            <span class="text-danger error-message">{{ $errors->first('content') }}</span>
                        @endif
                        <span class="error error-message"></span>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-md rounded font-sm hover-up save-newsletter-info">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@include('common.store_admin.footer')
<script>
    $(document).on("click",".save-newsletter-info",function() {
        check_fields = validateFields($(this));
        if(check_fields > 0)
            return false;
        else
            return true;
    });
</script>