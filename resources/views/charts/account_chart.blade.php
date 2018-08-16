
@push('scripts')
      {!! $chart->script() !!}
@endpush
<div class="card text-white bg-inverse">
{!! $chart->html() !!}
<div class="card-body pb-0 pt-2 pl-2 pull-left">

<div class="text-value">${{$account->getBalance()}}</div>
<div>{{$account->name}}</div>
</div>
</div>
<style>
.card-body.pb-0.pt-2.pl-2.pull-left {
    position: absolute;
    z-index: 30;
    font-weight: bold;
}
.card {     height: 150px;
    width: 300px;}
.card canvas { position:absolute; }
</style>
