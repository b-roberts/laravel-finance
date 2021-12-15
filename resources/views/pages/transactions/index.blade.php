@extends('templates.bootstrap')
@section('content')
<div class="row">
  <div class="col-2">
    <div style="height:2em;">
    <div id="q"></div>
  </div>
    <div id="ais-account"></div>
    <div id="ais-category"></div>
    <div id="ais-payee"></div>
    <div id="ais-direction"></div>
    <div id="ais-method"></div>
    <div id="refinement-list">
      <h3>Dates</h3>
      <div id="calendar" class="daterange"></div>
    </div>

  </div>
  <div class="col-10" id="hits">

  </div>
  </div>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/instantsearch.js@2.10.1/dist/instantsearch.min.css">
<script src="https://cdn.jsdelivr.net/npm/instantsearch.js@2.10.1"></script>

<script id="hitTemplate" type="text/html">
  <hr class="my-4" />
<div class="row my-4" >
  <div class="col-2">@{{date}}</div>
  <div class="col-4" >
    <a
      href="@{{url}}"
      onclick="$('#myiframe').attr('src',$(this).attr('href'));
        window.open($(this).attr('href'),'window','toolbar=no, menubar=no, resizable=no, height=500, width=700')"
      target="myiframe" data-toggle="modal" data-target="#myModal">
      <strong style="font-size:1.25em;">
        <i class="fas @{{icon}}"></i>
        @{{label}}
      </strong><br />
      <strong>@{{type}}</strong> @{{id}} <em>@{{allocation_type}} </em><br />
      <small><i class="far fa-credit-card"></i> @{{account.name}}</small>
    </a>
  </div>
  <div class="col-1">@{{value}}</div>

<div class="col-2">
@{{#categories}}
  <span  class="badge" style="background-color:@{{color}};color:@{{alt_color}}">
    @{{name}}
  </span>
  @{{/categories}}
</div>
<div class="col-2">


<!-- Default dropleft button -->
<div class=" dropleft">
  <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    ...
  </button>
  <div class="dropdown-menu">
    <a class="dropdown-item" onclick="viewLocation('@{{location}}')">See All @{{location}}</a>
    <a class="dropdown-item" href="{{route('rule.create')}}?match=%2F@{{location}}%2F" target="_blank">Create Rule Like This</a>
    <a class="dropdown-item" href="{{route('payee.create')}}?match=%2F@{{location}}%2F" target="_blank">Create Payee Like This</a>
<a class="dropdown-item" href="#">Delete</a>
  </div>
</div>
<input type="checkbox" data-transaction-check="@{{id}}" value="@{{id}}"/>
</div>
</div>
</script>

@push('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/moment.min.js"></script>
  <script src="https://unpkg.com/BaremetricsCalendar@1.0.11/public/js/Calendar.js"></script>
<script>

function viewLocation(location){

}


const customSearchClient = {
  search(requests) {
    return fetch('{{route('vue.transactions')}}', {
      method: 'post',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      body: JSON.stringify({ requests }),
    }).then(res => res.json());
  }
};

var search = instantsearch({

  indexName: 'instant_search',
  searchClient: customSearchClient,

});

search.addWidget(
  instantsearch.widgets.searchBox({
    container: '#q',
    placeholder: 'Search for products',
    autofocus: false,
    poweredBy: true,
    reset: true,
    loadingIndicator: false
  })
);

search.addWidget(
  instantsearch.widgets.hits({
    container: '#hits',
    templates: {
        empty: 'No results',
        item: $('#hitTemplate').html()
      }
  })
);

search.addWidget(
  instantsearch.widgets.menu({
    container: '#ais-direction',
    attributeName: 'direction',
    limit: 10,
    autoHideContiner: false,
    collapsible:false,
    templates: {
      header: '<p class="ais-header">Direction</p>'
    }
  })
);

search.addWidget(instantsearch.widgets.refinementList({
  container: '#ais-account',
  attributeName: 'account',
  autoHideContiner: false,
  collapsible:false,
  templates: {
    header: '<p class="ais-header">Account</p>'
  },
}));

 search.addWidget(instantsearch.widgets.refinementList({
  container: '#ais-category',
  attributeName: 'category',
  autoHideContiner: false,
  collapsible:false,
  templates: {
    header: '<p class="ais-header">Category</p>'
  },
}));


search.addWidget(instantsearch.widgets.refinementList({
 container: '#ais-payee',
 attributeName: 'payee',
     limit: 50,
 autoHideContiner: false,
 collapsible:false,
 templates: {
   header: '<p class="ais-header">Payee</p>'
 },
}));

search.addWidget(instantsearch.widgets.refinementList({
 container: '#ais-method',
 attributeName: 'method',
     limit: 50,
 autoHideContiner: false,
 collapsible:false,
 templates: {
   header: '<p class="ais-header">Method</p>'
 },
}));



const makeRangeWidget = instantsearch.connectors.connectRange(
  (options, isFirstRendering) => {
    if (!isFirstRendering) return;

    const { refine } = options;

    new Calendar({
      element: $('#calendar'),
      same_day_range: true,
      callback: function() {
        const start = new Date(this.start_date).getTime();
        const end = new Date(this.end_date).getTime();
        const actualEnd = start === end ? end + ONE_DAY_IN_MS - 1 : end;

        refine([start, actualEnd]);
      },
      // Some good parameters based on our dataset:
      start_date: new Date(),
      end_date: new Date('{{date('M/D/Y')}}'),
      earliest_date: new Date('01/01/2008'),
      latest_date: new Date('{{date('M/D/Y')}}'),
    });
  }
);

const dateRangeWidget = makeRangeWidget({
  attributeName: 'date',
});

search.addWidget(dateRangeWidget);

search.start();

var transactions=[];

</script>
@endpush
@endsection
