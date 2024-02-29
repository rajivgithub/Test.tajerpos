@if(!empty(Auth::user()->is_admin) && Auth::user()->is_admin == 3) 
    @include('common.cashier_admin.header')
@else
    @include('common.store_admin.header')
@endif    
<section class="content-main">
    <div class="content-header">
        <div>
            <h2 class="content-title card-title">Product Bulk Upload</h2>
        </div>
    </div>
    <div class="card mb-3 alert-warning">                   
        <div class="card-body">
            <p><b>Step 1:</b></p>
            <p>1. Download the skeleton file and fill it with proper data.</p>
            <p>2. You can download the example file to understand how the data must be filled.</p>
            <p>3. Once you have downloaded and filled the skeleton file, upload it in the form below and submit.</p>
            <p>4. After uploading products you need to edit them and set product's images and choices.</p>
        </div>
    </div>
    <a href="{{ URL::asset('assets/imgs/document/product_bulk_demo.xlsx') }}"><button class="btn btn-primary rounded font-sm hover-up text-light">Download CSV</button></a>
    <div class="card mb-3 alert-warning mt-4">                   
        <div class="card-body">
            <p><b>Step 2:</b></p>
            <p>1. Category and Brand should be in numerical id.</p>
            <p>2. You can download the pdf to get Category and Brand id.</p>
        </div>
    </div>
    <a href="{{ URL::asset('assets/imgs/document/category.pdf') }}" download ><button class="btn btn-primary rounded font-sm hover-up me-2 text-light">Download Category</button></a>
    <div class="card mb-3 mt-4"> 
        <div class="card-header">
            <h4>Upload Product File</h4>
        </div>			 
        <div class="card-body">
            <div class="input-upload mb-3">                                    
                <input class="form-control" type="file">
            </div>
            <button class="btn btn-primary rounded font-sm hover-up text-light">Upload CSV</button> 
        </div>
    </div>
</section> 
@if(!empty(Auth::user()->is_admin) && Auth::user()->is_admin == 3) 
    @include('common.cashier_admin.footer')
@else
    @include('common.store_admin.footer')
@endif 