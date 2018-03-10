@extends('templates.narrow')
@section('content')
<h1>New Account</h1>
{!! Form::open(['route'=>['account.store'],'method'=>'post']) !!}
{!! Form::bsInput('text','name',null,['required']) !!}
{!! Form::bsTextarea('description',null,['required']) !!}
  <button type="submit" class="btn btn-primary">Submit</button>
{!! Form::close(); !!}

@endsection
