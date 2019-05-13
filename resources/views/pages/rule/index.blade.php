@extends('templates.narrow')
@section('content')
<h1>My Rules</h1>

<table class="table table-striped">

@foreach($rules as $rule)
<tr>
  <td>{{$rule->match}}</td>
  <td>{{($rule->category) ? $rule->category->name : ''}}</td>
  <td><a href="{{route('account.edit',$rule->id)}}" class="btn btn-secondary">Edit</a></td>
  <td>{{$rule->minValue}} &mdash; {{$rule->maxValue}}</td>
  <td>{{$rule->percentage *100}}%</td>
</tr>
@endforeach
</table>
<a href="{{route('rule.create')}}" class="btn btn-primary">Add New Rule</a>
@endsection
