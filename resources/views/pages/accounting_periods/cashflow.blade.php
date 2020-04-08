@extends('templates.bootstrap')
@section('content')
  <div class="container">
    @foreach($charts as $chart)
        {!! $chart->html() !!}
  @endforeach
</div>
@endsection
@push('scripts')
  @foreach($charts as $chart)
    {!! $chart->script() !!}
  @endforeach
@endpush
