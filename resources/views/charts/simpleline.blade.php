<script type="text/javascript">
    var ctx = document.getElementById("{{ $model->id }}")
    var data = {
        labels: [
            @foreach($model->labels as $label)
                "{!! $label !!}",
            @endforeach
        ],
        datasets: [
            @for ($i = 0; $i < count($model->datasets); $i++)
                {
                    fill: false,
                    label: "{!! $model->datasets[$i]['label'] !!}",
                    lineTension: 0.3,
                    @if($model->colors and count($model->colors) > $i)
                        @php($c = $model->colors[$i])
                    @else
                        @php($c = sprintf('#%06X', mt_rand(0, 0xFFFFFF)))
                    @endif
                    borderColor: "{{ $c }}",
                    backgroundColor: "{{ $c }}",
                    data: [
                        @foreach($model->datasets[$i]['values'] as $dta)
                            {{ $dta }},
                        @endforeach
                    ],
                },
            @endfor
        ]
    };

    var myLineChart = new Chart(ctx, {
        type: 'line',
        data: data,

        options: {
          tooltips: {
            yPadding: 0,
            caretSize: 0,
            titleSpacing: 0,
            titleMarginBottom: 0,
            titleFontSize:10,
            bodyFontSize: 10,
            displayColors: false,
            mode: 'label'
          },
          responsive: false,
          maintainAspectRatio: false,
    legend: {
      display: false
    },
    scales: {
      xAxes: [{
        gridLines: {
          color: 'transparent',
          zeroLineColor: 'transparent'
        },
        ticks: {
          fontSize: 2,
          fontColor: 'transparent',
        }

      }],
      yAxes: [{
        gridLines: {
          color: 'transparent',
          zeroLineColor: '#dddddd'
        },
        ticks: {
          display: false,
          //min: 0,
          //max: Math.max.apply(Math, data.datasets[0].data) + 5,
        }
      }],
    },
    elements: {
      line: {
        borderWidth: 1
      },
      point: {
        radius: 0,
        hitRadius: 2,
        hoverRadius: 2,
      },
    }
        }
    });
</script>


<canvas id="{{ $model->id }}" height="20"    ></canvas>
