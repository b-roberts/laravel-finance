@extends('templates.narrow')
@section('content')
<h1>New Rule</h1>
{!! Form::open(['route'=>['rule.store'],'method'=>'post']) !!}
{!! Form::bsInput('text','match', isset($_GET['match']) ? $_GET['match']: null,['required','placeholder'=>'/regex/']) !!}
{!! Form::bsSelect('category_id',$categories) !!}
{!! Form::bsInput('text','percentage',1,['required']) !!}
{!! Form::bsInput('text','minValue',null,[]) !!}
{!! Form::bsInput('text','maxValue',null,[]) !!}
{!! Form::bsSelect('type',['payment'=>'Payment','transfer'=>'Transfer']) !!}
  <button type="submit" class="btn btn-primary">Submit</button>
{!! Form::close(); !!}

@endsection
