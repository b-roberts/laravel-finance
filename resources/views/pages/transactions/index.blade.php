@extends('templates.bootstrap')
@section('content')
  <div>
    <a href="{{route('import.index')}}" class="pull-right">Import from financial institution</a>
  </div>
@include('modules.transactions_table')
@endsection
