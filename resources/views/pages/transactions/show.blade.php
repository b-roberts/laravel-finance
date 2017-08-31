@extends('templates.bootstrap')
@section('content')
<div class="container">
  <h1 class="{{$transaction->type}}">
    {{$transaction->location}}
    <small>{{$transaction->date}}</small>
  </h1>
  <div class="row">
    <div class="col-md-6">
      <dl class="row">
        <dt class="col-sm-3">{{$transaction->value > 0 ? 'Expense' : 'Income' }}</dt>
        <dd class="col-sm-9 {{$transaction->value > 0 ? 'expense' : 'income' }}">
          ${{$transaction->value}}
        </dd>
        <dt class="col-sm-3">Account: </dt>
        <dd class="col-sm-9">
          {{-- $transaction->account->name --}}
        </dd>
        @if($transaction->type=='transfer')
          <dt class="col-sm-3">Transfer Account: </dt>
          <dd class="col-sm-9">
            {{-- $transaction->transferAccount->name --}}
          </dd>
        @endif
      </dl>


      {!! Form::model($transaction, ['route' => ['transaction.update', $transaction->id]]) !!}
        {!!Form::label('description', 'Note') !!}
        {!! Form::text('note[description]'); !!}
      {!! Form::close() !!}
    </div>
    <div class="col-md-6">
      @include('charts.budget-pie-graph',['categories'=>$transaction->categories])
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <table class="table">
        @foreach($transaction->categories as $category)
          <tr><td>{{$category->name}}</td><td>{{$category->pivot->value}}</td></tr>
        @endforeach
      </table>
    </div>
  </div>
</div>
@endsection
