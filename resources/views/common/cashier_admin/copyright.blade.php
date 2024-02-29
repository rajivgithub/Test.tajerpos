<footer class="main-footer">
    <div class="row">
        <div class="col-12 text-center">{{ trans('store-admin.copyrights', ['year' => date('Y'), 'company_name' => Auth::user()->company_name]) }}</div>
    </div>
</footer>