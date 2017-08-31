@php
$chart = \Charts::create('pie', 'google')
	->title('Category Breakdown')
	->elementLabel("Category")

	->responsive(false)
	->colors($categories->pluck('color')->values())
	->values($categories->map(function ($c) {
		if(isset($c->pivot))
		{
			return $c->pivot->value >  0 ? $c->pivot->value : 0;
		}
		elseif(isset($c->actual))
		{
			return $c->actual >  0 ? $c->actual : 0;
		}
		else {
			return 0;
		}
	}))
	->labels($categories->pluck('name')->values())
;
@endphp
{!!$chart->html()!!}
@push('scripts')
	{!!$chart->script() !!}
@endpush
