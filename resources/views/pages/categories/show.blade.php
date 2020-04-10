@extends('templates.narrow')
@section('content')
<div class="text-center my-3">
  <a href="{{route('category.show',$category->id-1)}}" class="btn btn-outline-primary  rounded-pill w-25 p-3" href="#">Prev</a>
  <a  href="{{route('category.show',$category->id+1)}}"class="btn btn-outline-primary rounded-pill w-25 p-3" href="#">Next</a>
</div>
    <h1>{{$category->name}}</h1>
    <div class="row">
      <div class="col-md-4">
        <div class="h6">Average Transaction Total:</div>
        <div class="text-money">{{money($averageTransactionTotal)}}</div>
      </div>
      <div class="col-md-4">
        <div class="h6">Average Allocation Value:</div>
        <div class="text-money">{{money($averageAllocation)}}</div>
      </div>
      <div class="col-md-4">
        <div class="h6">Last 2 month average:</div>
        <div class="text-money">{{money($twoMonthAverage)}}</div>
      </div>
    </div>
    {!! $charts['categorySpendingChart']->html() !!}
@endsection
@push('scripts')
  @foreach($charts as $chart)
    {!! $chart->script() !!}
  @endforeach
@endpush
