<div class="form-group {{($errors->has($name)) ? 'has-danger' : ''}}">
    {{ Form::label($name, null, ['class' => 'control-label']) }}
    {{ Form::input($type,$name, $value, array_merge(['class' => 'form-control'], $attributes)) }}
    @if($helpText)
      <small class="form-text text-muted">{{$helpText}}</small>
    @endif
    @if($errors->has($name))
      <div class="form-control-feedback">
        @foreach ($errors->get($name) as $message)
          {{$message}}<br />
        @endforeach
    </div>
    @endif
</div>
