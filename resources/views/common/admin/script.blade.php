<script>
    window.langTranslations = @json(trans('validation'));
    window.adminTranslations = @json(trans('admin'));
</script>
<script src="{{ URL::asset('assets/js/vendors/jquery-3.6.0.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/vendors/bootstrap.bundle.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/vendors/select2.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/vendors/perfect-scrollbar.js') }}"></script>
<script src="{{ URL::asset('assets/js/vendors/jquery.fullscreen.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/vendors/chart.js') }}"></script>
<script src="{{ URL::asset('assets/js/mainf9e3.js?v=1.1') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/js/custom-chart.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/dataTables.bootstrap5.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/select2.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script src="{{ URL::asset('assets/js/common.js') }}"></script>
<script>
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    $(document).ready(function() {
        $(".search-store").each(function() {
        $(".search-store").val('');
        });
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
    var store_search_table = $('#store-search-table').DataTable({
        lengthChange: false,
        buttons: [ 'copy', 'excel', 'pdf', 'colvis' ]
    });
    $(document).on("change",".search-store",function() {
        if($.fn.dataTable.isDataTable('#store-search-table'))
            store_search_table.destroy();
        _this = $(this);
        type = 'store_name';
        filter_value = _this.val();
        if(filter_value != '') {
            _this.closest("main").find(".body-content").css("display","none");
            store_search_table = $('#store-search-table').DataTable({
                "processing": true,
                "serverSide": true,
                "order": [[ 0, "desc" ]],
                "ajax": {
                    "url": "{{ route(config('app.prefix_url').'.admin.store.index')}}",
                    "dataType": "json",
                    "type": "get",
                    data: {type: type, filter_value : filter_value},
                },
                "columns": [
                    { "data": "id" },
                    { "data": "store_number" },
                    { "data": "store_name" },
                    { "data": "store_phone_number"},
                    { "data": "email"},
                    { "data": "store_validity_date"},
                    { "data": "action","orderable": false,"searchable":false},
                ]	 
            });
            _this.closest("main").find(".store-search-table").css('display','block');
        } else {
            $(this).closest("main").find(".body-content").css("display","block");
            _this.closest("main").find(".store-search-table").css('display','none');
        }
    });
</script>
<script>
    +function ($) {
        'use strict'
        var DataKey = 'Masteradmin.controlsidebar'
        var Default = {
            slide: true
        }
        var Selector = {
            sidebar: '.control-sidebar',
            data: '[data-toggle="control-sidebar"]',
            open: '.control-sidebar-open',
            bg: '.control-sidebar-bg',
            wrapper: '.wrapper',
            content: '.content-wrapper',
            boxed: '.layout-boxed'
        }
        var ClassName = {
            open: 'control-sidebar-open',
            fixed: 'fixed'
        }
        var Event = {
            collapsed: 'collapsed.controlsidebar',
            expanded: 'expanded.controlsidebar'
        }
        // ControlSidebar Class Definition
        var ControlSidebar = function (element, options) {
            this.element = element
            this.options = options
            this.hasBindedResize = false
            this.init()
        }
        ControlSidebar.prototype.init = function () {
            // Add click listener if the element hasn't been
            // initialized using the data API
            if (!$(this.element).is(Selector.data)) {
                $(this).on('click', this.toggle)
            }
            this.fix()
            $(window).resize(function () {
                this.fix()
            }.bind(this))
        }
        ControlSidebar.prototype.toggle = function (event) {
            if (event) event.preventDefault()
                this.fix()
            if (!$(Selector.sidebar).is(Selector.open) && !$('body').is(Selector.open)) {
                this.expand()
            } else {
                this.collapse()
            }
        }
        ControlSidebar.prototype.expand = function () {
            if (!this.options.slide) {
                $('body').addClass(ClassName.open)
            } else {
                $(Selector.sidebar).addClass(ClassName.open)
            }
            $(this.element).trigger($.Event(Event.expanded))
        }
        ControlSidebar.prototype.collapse = function () {
            $('body, ' + Selector.sidebar).removeClass(ClassName.open)
            $(this.element).trigger($.Event(Event.collapsed))
        }
        ControlSidebar.prototype.fix = function () {
            if ($('body').is(Selector.boxed)) {
                this._fixForBoxed($(Selector.bg))
            }
        }
        // Private
        ControlSidebar.prototype._fixForBoxed = function (bg) {
            bg.css({
                position: 'absolute',
                height: $(Selector.wrapper).height()
            })
        }
        // Plugin Definition
        function Plugin(option) {
            return this.each(function () {
                var $this = $(this)
                var data = $this.data(DataKey)
                if (!data) {
                    var options = $.extend({}, Default, $this.data(), typeof option === 'object' && option)
                    $this.data(DataKey, (data = new ControlSidebar($this, options)))
                }
                if (typeof option == 'string') data.toggle()
            })
        }
        var old = $.fn.controlSidebar
        $.fn.controlSidebar = Plugin
        $.fn.controlSidebar.Constructor = ControlSidebar
        // No Conflict Mode
        $.fn.controlSidebar.noConflict = function () {
            $.fn.controlSidebar = old
            return this
        }
        // ControlSidebar Data API
        $(document).on('click', Selector.data, function (event) {
            if (event) event.preventDefault()
            Plugin.call($(this), 'toggle')
        })
    }(jQuery) // End of use strict
</script>
<script>
    const chat_form = document.querySelector(".typing-area"),
    inputField = chat_form.querySelector(".chat-input"),
    sendBtn = chat_form.querySelector("button"),
    chatBox = document.querySelector(".admin-chat-box-area");
    CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    sendBtn.onclick = ()=>{
        insert_chat_url = chat_form.querySelector(".insert-chat-url").value;
        incoming_id = chat_form.querySelector(".incoming-msg-id").value;
        store_id = chat_form.querySelector(".user-store-id").value;
        message = inputField.value;
        $.ajax({
            url: insert_chat_url,
            type: 'post',
            data: {_token: CSRF_TOKEN,incoming_msg_id: incoming_id, message: message, store_id : store_id},
            success: function(response){
                inputField.value = "";
                scrollToBottom();
            }
        });
    }
    chatBox.onmouseenter = ()=>{
        chatBox.classList.add("active");
    }
    chatBox.onmouseleave = ()=>{
        chatBox.classList.remove("active");
    }
    function scrollToBottom(){
        chatBox.scrollTop = chatBox.scrollHeight;
    }
    $(document).ready(function() {
        var get_logo_url = $(".get-logo-image").val();
        module_name = $(".get-module-name").val();
        $.ajax({
        url: get_logo_url,
        type: 'post',
        data: {_token: CSRF_TOKEN,module_name: module_name},
        success: function(response){
            moduleLogos = response.moduleLogos;
            if (moduleLogos != null && Object.keys(moduleLogos).length > 1) {
                if(moduleLogos.company_logo != null)
                    $(".sidebar-logo").attr("src",moduleLogos.company_logo);
                if(moduleLogos.favicon != null)
                    $(".favicon-image").attr("href",moduleLogos.favicon);
            }
            $(".page-loader").hide();
        }
        });
    });
</script>