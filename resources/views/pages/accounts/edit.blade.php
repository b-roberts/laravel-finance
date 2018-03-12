@extends('templates.narrow')
@section('content')
<h1>{{$account->name}}</h1>
{!! Form::model($account,['route'=>['account.update',$account->id],'method'=>'put']) !!}
{!! Form::bsInput('text','name',['required']) !!}
{!! Form::bsTextarea('description',['required']) !!}
  <button type="submit" class="btn btn-primary">Submit</button>
{!! Form::close(); !!}

@endsection
