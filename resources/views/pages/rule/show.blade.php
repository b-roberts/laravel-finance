@extends('templates.narrow')
@section('content')
<h1>{{$account->name}}</h1>
        {!! $accountGraph->html() !!}
        @push('scripts')

            {!! $accountGraph->script() !!}

        @endpush
@endsection
