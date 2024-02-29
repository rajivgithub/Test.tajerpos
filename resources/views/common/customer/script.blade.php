<script>
    window.langTranslations = @json(trans('validation'));
    window.customerTranslations = @json(trans('customer'));
</script>
<script src="{{ URL::asset('assets/customer/js/vendor/jquery-3.6.0.min.js') }}"></script>
<!-- <script src="{{ URL::asset('assets/customer/js/vendor/jquery-migrate-3.3.2.min.js') }}"></script> -->
<script src="{{ URL::asset('assets/customer/js/vendor/bootstrap.bundle.min.js') }}"></script>
<script src="{{ URL::asset('assets/customer/js/slick.min.js') }}"></script>
<script src="{{ URL::asset('assets/customer/js/owl.carousel.min.js') }}"></script>
<script src="{{ URL::asset('assets/customer/js/wow.min.js') }}"></script>
<script src="{{ URL::asset('assets/customer/js/jquery.scrollup.min.js') }}"></script>
<!-- <script src="{{ URL::asset('assets/customer/js/jquery.nice-select.js') }}"></script> -->
<script src="{{ URL::asset('assets/customer/js/jquery.magnific-popup.min.js') }}"></script>
<!-- <script src="{{ URL::asset('assets/customer/js/mailchimp-ajax.js') }}"></script> -->
<script src="{{ URL::asset('assets/customer/js/jquery-ui.min.js') }}"></script>
<!-- <script src="{{ URL::asset('assets/customer/js/jquery.zoom.min.js') }}"></script> -->
<script src="{{ URL::asset('assets/customer/js/main.js') }}"></script>
<!--modernizr min js here-->
<script src="{{ URL::asset('assets/customer/js/vendor/modernizr-3.11.2.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/common.js') }}"></script>
<script src="{{ URL::asset('assets/js/validation.js') }}"></script>
<script src="{{ URL::asset('assets/js/toastr.min.js') }}"></script>
<script src="{{ URL::asset('assets/customer/js/common.js') }}"></script>
<script>
    $(document).ready(function() {
        CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
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
</script>