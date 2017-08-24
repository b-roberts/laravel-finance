@extends('templates.bootstrap')
@section('content')
<div class="container">
<h1 class="{{$transaction->type}}">
  {{$transaction->location}}
  <small>{{$transaction->date}}</small>
</h1>
<div class="{{$transaction->value > 0 ? 'expense' : 'income' }}">
  ${{$transaction->value}}</div>
<div>
  {{($transaction->note) ? $transaction->note->description : ''}}
</div>
<div class="row">
  <div class="col-md-6">
@foreach($transaction->categories as $category)
  <span class="badge" style="background-color:#{{$category->color}}">{{$category->name}}</span>
,
@endforeach
</div>
<div class="col-md-6">
@include('charts.budget-pie-graph',['dataTable'=>$transaction->toDataTableArray()])
</div>
</div>
</div>
@endsection
