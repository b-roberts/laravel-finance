@extends('templates.bootstrap')
@section('content')
<div class="container">
  <h1>
    Import
  </h1>
  {!! Form::open(['action' => 'ImportController@store', 'files' => true]) !!}
  <div class="alert alert-info">
    <p>Amcom SUCKS. use QFX for amcom, and no one else.</p>
  </div>
    <div class="form-group">
      <label for="sel-account">Account:</label>
      <select name="account_id"  class="form-control" id="sel-account">
        @foreach($accounts as $account)
          <option value="{{$account->id}}">{{$account->name}}</option>
        @endforeach
      </select>
    </div>
    <div class="form-group">
      <label for="file-statement">Statement File:</label>
      {!! Form::file('statement', ['class'=>'form-control-file', 'id'=>'file-statement']) !!}
    </div>
    {!! Form::submit('Submit', ['class'=>'btn btn-primary']) !!}
  {!! Form::close() !!}
</div>
@endsection
