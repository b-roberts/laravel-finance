@extends('templates.bootstrap')
@section('content')
<h1>{{$payee->name}}</h1>
<div class="row">
    <div class="col-md-8">
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                Average Amount:
                <p class="lead">{{number_format($stats['mode'], 2)}} <small>Mode</small></p>
                <p class="lead">{{number_format($stats['average'], 2)}} <small>Avg</small></p>
            </div>
        </div>
    </div>
</div>
@endsection
