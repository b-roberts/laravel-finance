@extends('templates.bootstrap')
@section('content')
  <style>
  .totalLine { border-top:solid 2px #000;

    font-style:italic;
    font-size:1.1em;
    }
    table tr td:first-child { padding-left: 4em;}
  </style>
  <div class="container">
@include('modules.date_pager',['startDate'=>$startDate])

<table class="table table-stripped">
  <tr>
    <th colspan="3">Income</th>
  </tr>
  @foreach($transactions->where('value','<',0)->groupBy('account_id') as $transaction)
    <tr>
      <td>{{$transaction->first()->account ? $transaction->first()->account->name : 'Other'}}</td>
      <td></td>
      <td class="income">{{$transaction->sum('value')*-1}}</td>
    </tr>
  @endforeach
  <tr class="totalLine">
    <td></td>
    <td></td>
    <td class="income">{{$incomeTransactions->where('type','payment')->sum('value')*-1}}</td>
  </tr>


@foreach($designations as $designation)
  @php $categories = $designation->categories->where('actual','<>',0); @endphp
  @if ($categories->count())
  <tr>
    <th colspan="3">{{$designation->name}}</th>
  </tr>
  @foreach($categories->sortByDesc('actual') as $category)
    <tr {{($categories->count()==1) ? 'class="totalLine"' : ''}}>
      <td>{{$category->name}}</td>
      <td>{!!  isset($charts[$category->id]) ?$charts[$category->id]->html() :''!!}</td>
      <td class="expense">({{$category->actual}})</td>
    </tr>
  @endforeach
    @if ($categories->count()>1)
  <tr class="totalLine">
    <td></td>
    <td></td>
    <td class="income">({{$categories->sum('actual')}})</td>
  </tr>
@endif
@endif
  @endforeach


  <tr class="totalLine">
    <td></td>
    <td></td>
    <td class="income"><strong>{{$transactions->sum('value')*-1}}</strong></td>
  </tr>
</table>

</div>
@include('modules.date_pager',['startDate'=>$startDate])
@endsection
@push('scripts')
  @foreach($charts as $chart)
    {!! $chart->script() !!}
  @endforeach
@endpush
