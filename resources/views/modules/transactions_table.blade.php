<table id="myTable" class="table tablesorter-bootstrap">
<thead>
<tr><th>date</th><th>location</th><th>Amount</th><th>Note</th><th>Category</th></tr>
</thead>
<tbody>
@foreach($transactions as $transaction)

<tr data-id="{{$transaction->id}}"><td>{{$transaction->date}}</td>
  <td class="{{$transaction->type}}">
    <a href="{{route('transaction.show',$transaction->id)}}">
    <strong>{{$transaction->location}}</strong><br />
{{$transaction->id}}
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
<button onclick=" if (confirm('Are you sure you want to delete this transaction?'))
  {
    $.ajax({ url:'{{route('transaction.destroy', $transaction->id)}}',
        type:'DELETE',
    success: function()
        {
$('[data-id=&quot;{{$transaction->id}}&quot;]').remove();
        }});
  }
">Delete</button>



</td>
</tr>

@endforeach
</tbody>
</table>
