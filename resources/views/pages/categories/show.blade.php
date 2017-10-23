@extends('templates.bootstrap')
@section('content')
  <div class="container">
        {!! $charts[0]->html() !!}

        {{$averageTransactionTotal}}<br />
        {{$averageAllocation}}<br />
        {{$twoMonthAverage}}
  </div>
@endsection
@push('scripts')
  @foreach($charts as $chart)
    {!! $chart->script() !!}
  @endforeach
@endpush
