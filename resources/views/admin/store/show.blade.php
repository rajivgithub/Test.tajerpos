<!DOCTYPE html>
<html lang="en">
    <head> 
        <title>{{ trans('admin.view_store_title', ['attribute' => !empty($store_details[0]->store_name) ? $store_details[0]->store_name : 'Store']) }}</title>
        @include('common.admin.header')
    </head>
    <body>
        <div class="page-loader"><div class="spinner"></div></div>  
        <div class="screen-overlay"></div>
        @include('common.admin.navbar')
        <main class="main-wrap">
            @include('common.admin.sidebar')
            <section class="content-main"> 
                @include('common.admin.search') 
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>{{ trans('admin.store_info') }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if(isset($store_details) && !empty($store_details))
                                <div class="col-lg-6">
                                    <table class="table table-bordered pay-sel"> 
                                        <tr>
                                            <td><b>{{ trans('admin.owner_name') }}</b></td>
                                            <td>{{ !empty($store_details[0]->store_user_name) ? $store_details[0]->store_user_name : '--' }}</td>
                                        </tr>
                                        <tr>
                                            <td><b>{{ trans('admin.shop_name') }}</b></td>
                                            <td>{{ !empty($store_details[0]->store_name) ? $store_details[0]->store_name : '--' }}</td>
                                        </tr>
                                        <tr>
                                            <td><b>{{ trans('admin.url') }}</b></td>
                                            <td>{{ !empty($store_details[0]->store_url) ? $store_details[0]->store_url  : '--' }}</td>
                                        </tr> 
                                        <tr>
                                            <td><b>{{ trans('admin.phone_number') }}</b></td>
                                            <td>{{ !empty($store_details[0]->store_phone_number) ? $store_details[0]->store_phone_number : '--' }}</td>
                                        </tr>
                                        <tr>
                                            <td><b>{{ trans('admin.email_address') }}</b></td>
                                            <td>{{ !empty($store_details[0]->email) ? $store_details[0]->email : '--' }}</td>
                                        </tr>
                                        <tr>
                                            <td><b>{{ trans('admin.password') }}</b></td>  
                                            <td>{{ !empty($store_details[0]->plain_password) ? decrypt($store_details[0]->plain_password) : '--' }}</td>
                                        </tr> 
                                        <tr>
                                            <td><b>{{ trans('admin.building_name') }}</b></td>
                                            <td>{{ !empty($store_details[0]->building_name) ? $store_details[0]->building_name : '--' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-lg-6">
                                    <table class="table table-bordered pay-sel">
                                        <tr>
                                            <td><b>{{ trans('admin.street_name') }}</b></td>
                                            <td>{{ !empty($store_details[0]->street_name) ? $store_details[0]->street_name : '--' }}</td>
                                        </tr>
                                        <tr>
                                            <td><b>{{ trans('admin.city') }}</b></td>
                                            <td>{{ !empty($store_details[0]->city_name) ? $store_details[0]->city_name : '--' }}</td>
                                        </tr>
                                        <tr>
                                            <td><b>{{ trans('admin.state') }}</b></td>
                                            <td>{{ !empty($store_details[0]->state_name) ? $store_details[0]->state_name : '--' }}</td>
                                        </tr>
                                        <tr>
                                            <td><b>{{ trans('admin.country') }}</b></td>
                                            <td>{{ !empty($store_details[0]->country_name) ? $store_details[0]->country_name : '--' }}</td>
                                        </tr>
                                        <tr>
                                            <td><b>{{ trans('admin.postal_code') }}</b></td>  
                                            <td>{{ !empty($store_details[0]->postal_code) ? $store_details[0]->postal_code : '--' }}</td>
                                        </tr> 
                                        <!-- <tr>
                                            <td><b>{{ trans('admin.validity_date') }}</b></td>
                                            <td>{{ !empty($store_details[0]->store_validity_date) ? date('m/d/Y', strtotime($store_details[0]->store_validity_date)) : '--' }}</td>
                                        </tr>
                                        <tr>
                                            <td><b>{{ trans('admin.validity') }}</b></td>
                                            <td>{{ !empty($store_details[0]->validity) ? $store_details[0]->validity : '--' }}</td>
                                        </tr> -->
                                        
                                        <tr>
                                            <td><b>{{ trans('admin.store_logo') }}</b></td> 
                                            <td><img src="{{ !empty($store_details[0]->store_logo) ? $store_details[0]->store_logo : '--' }}" class="img-sm img-thumbnail" alt="Item"></td>
                                        </tr>
                                    </table>
                                </div>
                            @endif
                        </div>
                        <div class="text-end">
                            <input type="hidden" class="store_id" value="{{ Crypt::encrypt($store_details[0]->store_id) }}">
                            <a href='{{ route(config("app.prefix_url").".admin.store.create",Crypt::encrypt($store_details[0]->store_id)) }}' class="edit-url"><button class="btn btn-success text-white rounded font-sm hover-up">{{ trans('admin.edit') }}</button></a>
                        </div>
                    </div>
                </div>
            </section>
            @include('common.admin.footer')
        </main>
        @include('common.admin.script')
    </body>
</html>