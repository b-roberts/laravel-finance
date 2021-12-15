@extends('templates.bootstrap')
@section('content')

<table class="table table-stripped text-right">
  <tr>
    <td>
    </td>
    @for($i=0; $i < 12; $i++)
    <td>{{ $startDate->copy()->addMonth($i)->format('M Y')}}</td>
    @endfor
    </tr>
  @foreach($accounts as $account)
    <tr>
      <td>
        {{$account->name}}
      </td>
      @for($i=0; $i < 12; $i++)
      <td>
        @isset($incomes[$i][$account->id])
          {{number_format(-1 * $incomes[$i][$account->id],2)}}
        @endisset
      </td>
      @endfor
        </tr>
  @endforeach

  <tr>
    <td>
    </td>
    @for($i=0; $i < 12; $i++)
    <td><strong>
      {{number_format(-1 *$incomes[$i]->sum(),2) }}
    </strong>
      </td>
      @endfor
  </tr>



    @foreach($designations as $designation)
    <tr>
      <td>
        {{$designation->name}}
      </td>
      @for($i=0; $i < 12; $i++)
      <td>
        @isset($expenses[$i][$designation->id])
          {{number_format($expenses[$i][$designation->id],2)}}
        @endisset
      </td>
      @endfor
        </tr>
          @endforeach
          <tr>
            <td>
              Unallocated
            </td>
            @for($i=0; $i < 12; $i++)
            <td>
              @isset($expenses[$i][99])
                {{number_format($expenses[$i][99],2)}}
              @endisset
            </td>
            @endfor
              </tr>
  <tr>
    <td>
    </td>
    @for($i=0; $i < 12; $i++)
    <td><strong>
      {{number_format($expenses[$i]->sum(),2) }}
      </strong>
      </td>
      @endfor
  </tr>


  <tr>
    <td>
    </td>
    @for($i=0; $i < 12; $i++)
    <td><strong>
      {{number_format((-1 *$incomes[$i]->sum()) - $expenses[$i]->sum(),2) }}
      </strong>
      </td>
      @endfor
  </tr>
</table>


@endsection
