@extends('templates.bootstrap')
@section('content')
<div class="container">
  <h1 class="{{$transaction->type}}">
    {{$transaction->location}}
    <small>{{$transaction->date}}</small>
  </h1>
  <div class="row">
    <div class="col-md-6">
      <dl class="row">
        <dt class="col-sm-3">{{$transaction->value > 0 ? 'Expense' : 'Income' }}</dt>
        <dd class="col-sm-9 {{$transaction->value > 0 ? 'expense' : 'income' }}">
          ${{$transaction->value}}
        </dd>
        <dt class="col-sm-3">Account: </dt>
        <dd class="col-sm-9">
          @if($transaction->account)
            {{ $transaction->account->name }}
          @endif
        </dd>
        @if($transaction->type=='transfer')
          <dt class="col-sm-3">Transfer Account: </dt>
          <dd class="col-sm-9">
            {{-- $transaction->transferAccount->name --}}
          </dd>
        @endif
      </dl>


      {!! Form::model($transaction, ['route' => ['transaction.update', $transaction->id]]) !!}
      {{ method_field('PUT') }}
        {!!Form::label('description', 'Note') !!}
        {!! Form::text('note[description]'); !!}
        <div class="control-group">

            <div class="controls" id="fields">
            <div class=" input-group col-xs-3">
                <label class="col" name="fields[]"  >
                  Total:
                </label>
                <span class="col">$<span id="total">{{$transaction->value}}</span></span>
              </div>

              @foreach($transaction->categories as $transactionCategory)
              <div class="entry input-group col-xs-3">
                <select class="form-control" name="category[]"  >
                  @foreach($categories as $category)
                  <option value="{{$category->id}}"
                  @if ($transactionCategory->id == $category->id)
                  selected="selected"
                  @endif
                  >{{$category->name}}
                  </option>
                  @endforeach

                </select>
                <input class="form-control" name="value[]" type="text"  value="{{$transactionCategory->pivot->value}}"/>
                <span class="input-group-btn">
                      <button class="btn btn-remove" type="button">
                          -
                      </button>
                  </span>
              </div>

              @endforeach

              <div class="entry input-group col-xs-3">
                <select class="form-control" name="category[]"  >
                <option></option>
                  @foreach($categories as $category)
                    <option value="{{$category->id}}">
                      {{$category->name}}
                    </option>
                  @endforeach
                </select>
                <input class="form-control" name="value[]" type="text"  value=""/>
                <span class="input-group-btn">
                      <button class="btn btn-add" type="button">
                          +
                      </button>
                  </span>
              </div>


            </div>
            <div class="controls">
            <div class=" input-group col-xs-3">
                <label class="col" name="fields[]"  >
                  Remainder:
                </label>
                <span class="col" id="remainder">
                  {{$transaction->value - $transaction->categories->sum('pivot.value')}}
                </span>
              </div>
              </div>
        </div>

        <button type="submit">Save</button>
      {!! Form::close() !!}
    </div>
    <div class="col-md-6">
      @include('charts.budget-pie-graph',['categories'=>$transaction->categories])
      <h2>Prediction</h2>
      @include('charts.budget-pie-graph',['categories'=>$prediction])

    </div>
  </div>

</div>
<script
  src="https://code.jquery.com/jquery-3.2.1.min.js"
  integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
  crossorigin="anonymous"></script>


<script>
$(function()
{
//onblur='evaluateLine($categoryID);' onkeypress='onEnter(event,function(){evaluateLine($categoryID)});'
$(document).on('keypress', 'input', function(evt)
{
var keyCode = evt ? (evt.which ? evt.which : evt.keyCode) : event.keyCode;
	  if (keyCode == 13) {
evaluateLine(this);
if(event.shiftKey){
  console.log('SUBMIT')
  $('form:first').submit();
}
return false;

}

});


    $(document).on('click', '.btn-add', function(e)
    {
        e.preventDefault();

        var controlForm = $('#fields'),
            currentEntry = $(this).parents('.entry:first'),
            newEntry = $(currentEntry.clone()).appendTo(controlForm);

        newEntry.find('input').val('');
        controlForm.find('.entry:not(:last) .btn-add')
            .removeClass('btn-add').addClass('btn-remove')
            .removeClass('btn-success').addClass('btn-danger')
            .html('<span class="glyphicon glyphicon-minus"></span>');

      DisableOptions(); //disable selected values
    })
    .on('click', '.btn-remove', function(e)
    {
      $(this).parents('.entry:first').remove();
      DisableOptions(); //disable selected values
      e.preventDefault();
      return false;
    });
    $(document).on('change','select',function()
    {
      DisableOptions(); //disable selected values
    });
    DisableOptions();
  });



function DisableOptions()
{
$("select option").prop("disabled",false); //enable everything
    var arr=[];
      $('option:selected').each(function(){
      var e = $(this).val();
        $('option[value="' + e + '"]').prop('disabled',true);
        $(this).prop('disabled',false);
       } );


}



function asMoney(input)
{
return parseFloat(input).toFixed(2);
}


function onEnter(evt,callback)
{
	var keyCode = evt ? (evt.which ? evt.which : evt.keyCode) : event.keyCode;
	  if (keyCode == 13) {
    callback();
    }
}

function evaluateLine(line)
{
 var x = $(line).val();
if(x=='x'){
  x=parseFloat($('#total').html());
}

		  var v =  new Function('return ' + x + ';')();
		  if (typeof v !== NaN && v >0)
			  {
		  $(line).val(asMoney(v));
			  }
	  calculateRemaining();

}
function calculateRemaining()
{
	var remainder=parseFloat($('#total').html());
	$('input[name="value[]"]').each(function(idx,elem)
	{
		remainder = remainder - $(this).val();
	});
	remainder = remainder * 100;
	remainder = Math.round(remainder);
	remainder = remainder / 100;
	$('#remainder').html(remainder);
	return remainder;
}
</script>
@endsection
