@extends('templates.bootstrap')
@section('content')




<h1>Monthly Budget</h1>

<div class="row">
	<div class="span6">
		@include('charts.budget-pie-graph',['dataTable'=>$budget->toDataTableArray()])
		<div id="budgetTable" style="width: 460px;"></div>
	</div>
	<div class="span6">
	<div id="incomeChart" style="width: 460px; height: 460px;"></div>
	<table class="table">
	<tr><th>Total Monthly Income</th><th>Total Monthly Expenses</th><th>Balance</th></tr>

	</table>
	</div>
</div></div>



@endsection
