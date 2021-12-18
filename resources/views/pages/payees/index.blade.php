@extends('templates.narrow')
@section('content')
<h1>My Payees</h1>
<a href="{{route('payee.create')}}" class="btn btn-primary">Add New Payee</a>
<table class="table table-striped">
<tr><th>Name</th><th>Regex</th><th>Transaction Count</th><th>Total Spend</th></tr>
@foreach($payees as $payee)
<tr>
  <td><a href="{{route('payee.show',$payee->id)}}">{{$payee->name}}</a></td>
  <td>{{$payee->regex}}</td>
  <td>{{$payee->transactions_count}}</td>
  <td class="text-money">{{money($payee->total_spend_count)}}</td>
  <td><a href="{{route('payee.edit',$payee->id)}}" class="btn btn-secondary">Edit</a></td>
</tr>
@endforeach
</table>
<a href="{{route('payee.create')}}" class="btn btn-primary">Add New Payee</a>
@endsection
