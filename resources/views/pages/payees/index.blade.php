@extends('templates.narrow')
@section('content')
<h1>My Payees</h1>
<a href="{{route('payee.create')}}" class="btn btn-primary">Add New Payee</a>
<table class="table table-striped">

@foreach($payees as $payee)
<tr>
  <td>{{$payee->name}}</td>
  <td>{{$payee->regex}}</td>
  <td>{{$payee->transactions_count}}</td>
  <td class="text-money">{{money($payee->total_spend_count)}}</td>
  <td><a href="{{route('payee.edit',$payee->id)}}" class="btn btn-secondary">Edit</a></td>
</tr>
@endforeach
</table>
<a href="{{route('payee.create')}}" class="btn btn-primary">Add New Payee</a>
@endsection
