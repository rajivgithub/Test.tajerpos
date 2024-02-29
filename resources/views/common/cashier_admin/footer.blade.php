<script>
    window.langTranslations = @json(trans('validation'));
    window.datatableTranslations = @json(trans('datatables'));
    window.translations = @json(trans('store-admin'));
</script>
<script src="{{ URL::asset('assets/cashier-admin/js/vendors.min.js') }}"></script>
<script src="{{ URL::asset('assets/cashier-admin/vendor_components/apexcharts-bundle/dist/apexcharts.min.js') }}"></script>  	
<script src="{{ URL::asset('assets/cashier-admin/vendor_components/datatable/datatables.min.js') }}"></script>
<script src="{{ URL::asset('assets/cashier-admin/vendor_components/OwlCarousel2/dist/owl.carousel.js') }}"></script>
<script src="{{ URL::asset('assets/cashier-admin/js/template.js') }}"></script>
<script src="{{ URL::asset('assets/cashier-admin/js/pages/dashboard.js') }}"></script>
<script src="{{ URL::asset('assets/cashier-admin/js/pages/data-table.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script src="{{ URL::asset('assets/js/vendors/select2.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/chat.js') }}"></script>
<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.min.js"></script>
<script src="{{ URL::asset('assets/js/common.js') }}"></script>
<script>
$(document).ready(function() {
    $(".page-loader").hide();
    CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    var table = $('#example').DataTable( {
        rowReorder: {
            selector: 'td:nth-child(2)'
        },
        responsive: true
    });
    @if(Session::has('message'))
        toastr.options =
        {
            "closeButton" : true,
            "progressBar" : true
        }
        toastr.success("{{ session('message') }}");
    @endif
    @if(Session::has('error'))
        toastr.options =
        {
            "closeButton" : true,
            "progressBar" : true
        }
        toastr.error("{{ session('error') }}");
    @endif
    @if(Session::has('info'))
        toastr.options =
        {
            "closeButton" : true,
            "progressBar" : true
        }
        toastr.info("{{ session('info') }}");
    @endif
    @if(Session::has('warning'))
        toastr.options =
        {
            "closeButton" : true,
            "progressBar" : true
        }
        toastr.warning("{{ session('warning') }}");
    @endif
});
// window.onload = function() {
//   $(".page-loader").hide();
// };
</script>