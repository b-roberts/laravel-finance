@extends('templates.bootstrap')
@section('content')
  <div class="container">

    <h1>{{$category->name}}</h1>
    <div class="row">
      <div class="col-md-4">
        <div class="h6">Average Transaction Total:</div>
        <div>{{$averageTransactionTotal}}</div>
      </div>
      <div class="col-md-4">
        <div class="h6">Average Allocation Value:</div>
        <div>{{$averageAllocation}}</div>
      </div>
      <div class="col-md-4">
        <div class="h6">Last 2 month average:</div>
        <div>{{$twoMonthAverage}}</div>
      </div>
    </div>
    {!! $charts['cs']->html() !!}
  </div>
@endsection
@push('scripts')
  @foreach($charts as $chart)
    {!! $chart->script() !!}
  @endforeach
@endpush
