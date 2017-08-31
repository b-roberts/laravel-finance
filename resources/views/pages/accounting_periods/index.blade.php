@extends('templates.bootstrap')
@section('content')
  <div class="container">
@include('modules.date_pager',['startDate'=>$startDate])
  <div class="row">
    <div class="col-4">
      {!! $charts['spendPercentage']->html() !!}
      {!! $charts['expectedExpensePercentage']->html() !!}
      {!! $charts['expectedIncomePercentage']->html() !!}

    </div>
    <div class="col-8">
      {!! $charts['categoryBreakdown']->html() !!}
    </div>
  </div>
    <div class="row">
      <div class="col">
        {!! $charts['categoryBalance']->html() !!}
      </div>
    </div>
    <div class="row">
      <div class="col">
        {!! $charts['spendingByDay']->html() !!}
      </div>
    </div>
@include('modules.date_pager',['startDate'=>$startDate])
    @include('modules.transactions_table')
@include('modules.date_pager',['startDate'=>$startDate])
</div>

@endsection
@push('scripts')
  @foreach($charts as $chart)
    {!! $chart->script() !!}
  @endforeach
@endpush
