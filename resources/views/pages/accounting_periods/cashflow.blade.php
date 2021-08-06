@extends('templates.bootstrap')
@section('content')
  <div class="container">
    <div class="alert alert-info">
      <strong>Note:</strong> Exceptional Transactions are excluded from averages
    </div>
    
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
