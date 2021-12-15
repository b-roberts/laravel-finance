@extends('templates.bootstrap')
@section('content')

<table class="table table-stripped text-right">
  <tr>
    <td>
    </td>
    @for($i=0; $i < 12; $i++)
    <td>
      <a href="{{route('income-statement',[$startDate->copy()->addMonth($i)->format('Y-m-d')])}}">
        {{ $startDate->copy()->addMonth($i)->format('M Y')}}
      </a>
      @if($notes[$i]->count())
      <button class="btn btn-link"  data-toggle="modal" data-target="#notes-{{$i}}">
        <i class="fa fa-info-circle"></i>
      </button>
      @endif
    </td>
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

@for($i=0; $i < 12; $i++)
  @if($notes[$i]->count())
  <div class="modal fade" id="notes-{{$i}}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          @foreach($notes[$i] as $note)
            {{$note->body}}
            <em>{{$note->created_at->format('M d, Y H:i:s')}}</em>
          @endforeach
        </div>
      </div>
    </div>
  </div>
  @endif
@endfor

@endsection
