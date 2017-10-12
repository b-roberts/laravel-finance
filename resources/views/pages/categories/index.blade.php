@extends('templates.bootstrap')
@section('content')
  <div class="container">
  <h1>{{$category->name}}</h1>
        {!! $charts['cashflow']->html() !!}

</div>
@endsection
@push('scripts')
  @foreach($charts as $chart)
    {!! $chart->script() !!}
  @endforeach
@endpush
