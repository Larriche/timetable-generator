@extends('layouts.app')

@section('title')
Dashboard
@endsection

@section('content')
<div class="row tile_count">
    <?php $count = 1; ?>
    @foreach ($data['cards'] as $card)
    <div class="col-md-3 col-sm-4 col-xs-6 tile_stats_count">
        <a href="{{ $card['url'] }}">
            <span class="count_top" style="color: {{ $card['color'] }}"><i class="fa fa-{{$card['icon'] }}"></i> {{ $card['title'] }}</span>
            <div class="count" style="color: {{ $card['color'] }}">{{ $card['value'] }}</div>
        </a>
    </div>
    @endforeach
</div>

<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="x_panel">
	        <div class="x_content">
                <div class="row margin-bottom" style="margin-top: 50px">
                    <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12 col-lg-offset-3 col-md-offset-3 col-sm-offset-4 text-center">
                        <button class="btn btn-success btn-lg btn-block" id="resource-add-button">
                            <i class="fa fa-calendar"></i>  Generate New Timetables</button>
                    </div>

                    <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
                        <a class="btn btn-warning btn-lg btn-block" href="/reports">
                            <i class="fa fa-file"></i> View Reports</button>
                        </a>
                    </div>
                </div>

                <div id="resource-container">
                    @include('dashboard.timetables')
                </div>
                @include('dashboard.modals')
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{URL::asset('/js/app.js')}}"></script>
<script src="{{URL::asset('/js/dashboard/index.js')}}"></script>
<script src="{{URL::asset('/js/timetables/index.js')}}"></script>
@endsection