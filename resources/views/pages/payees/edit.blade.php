@extends('templates.narrow')
@section('content')
<h1>{{$payee->name}}</h1>
{!! Form::model($payee,['route'=>['payee.update',$payee->id],'method'=>'put']) !!}
{!! Form::bsInput('text','name',null,['required']) !!}
{!! Form::bsInput('text','regex',null,['required','placeholder'=>'/regex/']) !!}
  <button type="submit" class="btn btn-primary">Submit</button>
{!! Form::close(); !!}

@endsection
