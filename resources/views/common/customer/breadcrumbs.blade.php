<div class="breadcrumbs_aree breadcrumbs_bg mb-70" data-bgimg="{{ URL::asset('assets/customer/images/others/breadcrumbs-bg.png') }}"> 
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="breadcrumbs_text">
                    @if(isset($breadcrumbs) && !empty($breadcrumbs))
                        <ul>
                            @foreach ($breadcrumbs as $breadcrumb)
                                @if (!$loop->last)
                                    <li><a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['name'] }}</a></li>
                                    <span> &raquo; </span>
                                @else
                                    <li>{{ $breadcrumb['name'] }}</li>
                                @endif
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>