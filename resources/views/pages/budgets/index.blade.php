@extends('templates.bootstrap')
@section('content')
<table id="myTable" class="table tablesorter-bootstrap"> 
<thead>
<tr><th>date</th><th>location</th><th>Amount</th><th>Note</th><th>Category</th></tr>
</thead>
<tbody>
@foreach($budgets as $budget)

<tr><td><a href="{{route('budget.show',[$budget->id])}}">View</a></td><td>{{$budget->start_date}}</td><td>{{$budget->end_date}}</td><td>{{$budget->monthly_income}}</td></tr>

@endforeach
</tbody>
</table>
@endsection