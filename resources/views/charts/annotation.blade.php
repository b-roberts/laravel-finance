<script type="text/javascript">
google.charts.load('current', {'packages':['annotationchart']});
    chart = google.charts.setOnLoadCallback(drawCharta)

    function drawCharta() {
        var data = new google.visualization.DataTable();
        data.addColumn('date', 'Date');
        data.addColumn('number', 'Kepler-22b mission');
        data.addColumn('string', 'Kepler title');
        data.addColumn('string', 'Kepler text');
        data.addColumn('number', 'Gliese 163 mission');
        data.addColumn('string', 'Gliese title');
        data.addColumn('string', 'Gliese text');

        @foreach($model->transactions() as $date=>$trans)
        data.addRows([
          [new Date({{$date}}),
           {{$trans}}, undefined, undefined,
                                  {{$trans}}, undefined, undefined]]);
        @endforeach


        var chart = new google.visualization.AnnotationChart(document.getElementById("{{ $model->id }}"))

        var options = {
          displayAnnotations: true,
          min:0
        };

        chart.draw(data, options)
    }
</script>
<div id="{{$model->id}}"></div>
