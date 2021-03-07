@extends('layouts.app')

@section('content')
@if (isset($sidebar_content))
    <div class="container-fluid bg-blue-500" style="height:calc(100vh - 125px)">
@else
    <!-- <div class="container bg-blue-500"> -->
    <div class="container-fluid bg-blue-500" style="height:calc(100vh - 125px)">
@endif
    @php
        echo App\Http\Controllers\KanbanController::index()
    @endphp
    <div class="ml-1 mr-10">
        <div>
        <!-- <div class="col-md-10 col-md-offset-1"> -->
            @if (isset($content))
            <div class="panel panel-default">
                <div class="panel-heading clearfix">
                    <h3 class="panel-title pull-left" style="padding-top: 7.5px;">{!! $panel_header !!}</h3>
                    @if (isset($back))
                        <div class="pull-right">
                            {!! $back !!}
                        </div>
                    @endif
                </div>
                <div class="panel-body">
                    {!! $content !!}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('view.scripts')
<script type="text/javascript">
    $(document).ready(function() {
        if (noshdata.message_action !== '') {
            if (noshdata.message_action.search(noshdata.error_text) == -1) {
                toastr.success(noshdata.message_action);
            } else {
                toastr.error(noshdata.message_action);
            }
        }
        $('[data-toggle=offcanvas]').css('cursor', 'pointer').click(function() {
            $('.row-offcanvas').toggleClass('active');
        });
        if (noshdata.home_url == window.location.href) {
            $('#search_patient').focus();
        }
    });
</script>
@endsection
