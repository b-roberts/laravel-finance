@extends('templates.narrow')
@section('content')
<h1>My Accounts</h1>

<table>

@foreach($accounts as $account)
<tr>
  <td>{{$account->name}}</td>
  <td>{{$account->description}}</td>
  <td><a href="{{route('account.edit',$account->id)}}" class="btn btn-secondary">Edit</a></td>
  <td>
      @include('charts.account_chart',['account'=>$account])
  </td>
  <td>
    {!! Form::model($account,['route'=>['account.update',$account->id],'method'=>'put']) !!}<form action="{{route('account.update',$account->id)}}">
      <button class="btn" name="close" type="submit" value="1">Close</button>
    {!! Form::close(); !!}
  </td>
</tr>
@endforeach
</table>
<a href="{{route('account.create')}}" class="btn btn-primary">Add New Account</a>
@endsection
