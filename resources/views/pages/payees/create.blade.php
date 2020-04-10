@extends('templates.narrow')
@section('content')
<h1>New Payee</h1>
{!! Form::open(['route'=>['payee.store'],'method'=>'post']) !!}
{!! Form::bsInput('text','regex',null,['required','placeholder'=>'/regex/']) !!}
{!! Form::bsInput('text','name',null,['required']) !!}

  <button type="submit" class="btn btn-primary">Submit</button>
{!! Form::close(); !!}

@endsection
