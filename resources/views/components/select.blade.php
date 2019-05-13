
<div class="form-group {{ $errors->has($name) ? ' text-danger' : '' }}">
    {{ Form::label($selectAttributes['label'] ?? $name, null, ['class' => 'control-label']) }}
    {{ Form::select(
        $name,
        $list,
        $selected,
        array_merge(['class' => 'form-control ' . ($errors->has($name) ? 'is-invalid' :'')], $selectAttributes),
        array_merge([],$optionsAttributes)
        ) }}
    @if ($errors->has($name))
      <span class="form-control-feedback">
        <strong>{{ $errors->first($name) }}</strong>
      </span>
    @endif
</div>
