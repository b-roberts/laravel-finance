@extends('templates.bootstrap')
@section('content')
  <style>
  .totalLine { border-top:solid 2px #000;

    font-style:italic;
    font-size:1.1em;
    }
    .grandTotalLine { border-top:double 3px #000; font-size:1.2em;}
    .subTotalLine { border-top:solid 1px #000;

      font-style:italic;
      font-size:1.1em;
      }
        table tr.subTotalLine td:first-child { padding-left: 2em;}

    table tr td:first-child { padding-left: 4em;}
    table { font-size:inherit;}
    .income,.expense { text-align:right;}
    @media print {
      body { font-size:12px!important;}
      * { line-height: 14px; padding-top:0!important;padding-bottom:0!important;}
    }
  </style>
  <div class="container">
@include('modules.date_pager',['startDate'=>$startDate])
{!! $designationChart->html() !!}
<table class="table table-stripped">
  <tr>
    <th colspan="3">Income</th>
  </tr>
  @foreach($transactions->where('value','<',0)->groupBy('account_id') as $transaction)
    @foreach($transaction as $trans)
      <tr class="hdden-print">
        <td>{{$trans->location }}</td>
        <td></td>
        <td class="income">{{sprintf('%01.2f',$trans->value*-1)}}</td>
      </tr>
    @endforeach
    <tr class="subTotalLine">
      <td>{{$transaction->first()->account ? $transaction->first()->account->name : 'Other'}}</td>
      <td></td>
      <td class="income">{{sprintf('%01.2f',$transaction->sum('value')*-1)}}</td>
    </tr>
  @endforeach
  <tr class="totalLine">
    <td>Total Income</td>
    <td></td>
    <td class="income"><strong>{{sprintf('%01.2f',$incomeTransactions->where('type','payment')->sum('value')*-1)}}</strong></td>
  </tr>


@foreach($designations as $designation)
  @php $categories = $designation->categories->where('actual','<>',0); @endphp
  @if ($categories->count())
  <tr>
    <th colspan="3">{{$designation->name}}</th>
  </tr>
  @foreach($categories->sortByDesc('actual') as $category)
    <tr class="hidden-parint">
      <td>{{$category->name}}</td>
      <td><div class="hidden-print d-print-none">{!!  isset($charts[$category->id]) ?$charts[$category->id]->html() :''!!}</div></td>
      <td class="expense"> <i class=" {{ $category->changeIcon}}" title="({{sprintf('%01.2f',$category->previous)}})"></i> ({{sprintf('%01.2f',$category->actual)}})</td>
    </tr>
  @endforeach

  <tr class="totalLine hiddn-print">
    <td></td>
    <td></td>
    <td class="income">({{sprintf('%01.2f',$categories->sum('actual'))}})</td>
  </tr>

@endif
  @endforeach

      <tr>
      <td>UnAllocated</td>
      <td></td>
      <td class="expense">({{sprintf('%01.2f',$unallocatedTransactions->sum('value'))}})</td>
    </tr>

<tr class="totalLine">
  <td>Total Expense</td>
  <td></td>
  <td class="income"><strong>({{sprintf('%01.2f',$transactions->where('value','>',0)->sum('value'))}})</strong></td>
</tr>
  <tr class="grandTotalLine">
    <td>Grand Total</td>
    <td></td>
    <td class="income"><strong>{{sprintf('%01.2f',$transactions->sum('value')*-1)}}</strong></td>
  </tr>
</table>

</div>
<div class="hidden-print">
@include('modules.date_pager',['startDate'=>$startDate])
</div>
@endsection
@push('scripts')
  @foreach($charts as $chart)
    {!! $chart->script() !!}
  @endforeach
  {!! $designationChart->script() !!}
@endpush
