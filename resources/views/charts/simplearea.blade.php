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
        type: 'bar',
        data: data,

        options: {
          tooltips: {
              enabled:false,
              mode: index
          },
          responsive: false,
          maintainAspectRatio: true,
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

<canvas id="{{ $model->id }}"     ></canvas>
