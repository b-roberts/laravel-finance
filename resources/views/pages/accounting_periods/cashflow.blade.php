@extends('templates.bootstrap')
@section('content')
  <div class="container">
        {!! $charts['cashflow']->html() !!}
        {!! $charts['netIncome']->html() !!}
        {!! $charts['netWorth']->html() !!}
</div>
@endsection
@push('scripts')
  @foreach($charts as $chart)
    {!! $chart->script() !!}
  @endforeach
@endpush
