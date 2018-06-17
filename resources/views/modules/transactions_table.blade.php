<table id="myTable" class="table tablesorter-bootstrap">
<thead>
<tr><th>date</th><th>location</th><th>Amount</th><th>Note</th><th>Category</th></tr>
</thead>
<tbody>
@foreach($transactions as $transaction)

<tr data-id="{{$transaction->id}}" class="{{$transaction->allocation_type}}"><td>{{$transaction->date}}</td>
  <td class="{{$transaction->type}}">
    <a href="{{route('transaction.show',$transaction->id)}}"
      onclick="$('#myiframe').attr('src',$(this).attr('href'));

      window.open($(this).attr('href'),'window','toolbar=no, menubar=no, resizable=no, height=500, width=700')
      " target="myiframe" data-toggle="modal" data-target="#myModal">
    <strong>{{$transaction->location}}</strong><br />
{{$transaction->id}} {{$transaction->allocation_type}}
</a>
  </td>

  <td class="{{$transaction->value > 0 ? 'expense' : 'income' }}">{{$transaction->value}}</td>
<td>
{{($transaction->note) ? $transaction->note->description : ''}}
</td>
<td>
@foreach($transaction->categories as $category)
  @include('modules.category_badge')
@endforeach
</td>
<td>


  <!-- Default dropleft button -->
  <div class="btn-group dropleft">
    <button type="button" class="btn btn-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      ...
    </button>
    <div class="dropdown-menu">
      <a class="dropdown-item" href="{{route('payee',$transaction->payee_id)}}">See All {{$transaction->location}}</a>
<a class="dropdown-item" href="#" onclick=" if (confirm('Are you sure you want to delete this transaction?'))
  {
    $.ajax({ url:'{{route('transaction.destroy', $transaction->id)}}',
        type:'DELETE',
    success: function()
        {
$('[data-id=&quot;{{$transaction->id}}&quot;]').remove();
        }});
  }
">Delete</a>
    </div>
  </div>












</td>
</tr>

@endforeach
</tbody>
</table>
