@php $chartID = 'chart-' . mt_rand(); @endphp
	<script>
	$(function(){
google.charts.setOnLoadCallback(function() {
  budgetData = google.visualization.arrayToDataTable({!!$dataTable!!});
    var options = {
      title: 'My Daily Activities'
    };

    var budgetChart = new google.visualization.PieChart(document.getElementById('{{$chartID}}'));
    budgetChart.draw(budgetData, options);
})})
</script>

<div id="{{$chartID}}" style="width: 1000px; height: 1000px;"></div>
