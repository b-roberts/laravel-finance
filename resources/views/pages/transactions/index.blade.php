@extends('templates.bootstrap')
@section('content')
<div class="row">
  <div class="col-2">
    <div style="height:2em;">
    <div id="q"></div>
  </div>
    <div id="ais-category"></div>
    <div id="ais-payee"></div>
    <div id="ais-direction"></div>
  </div>
  <div class="col-10" id="hits">

  </div>
  </div>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/instantsearch.js@2.10.1/dist/instantsearch.min.css">
<script src="https://cdn.jsdelivr.net/npm/instantsearch.js@2.10.1"></script>

<script id="hitTemplate" type="text/html">
<div class="row" >
  <div class="col-2">@{{date}}</div>
  <div class="col-4" >
    <a
      href="@{{url}}"
      onclick="$('#myiframe').attr('src',$(this).attr('href'));
        window.open($(this).attr('href'),'window','toolbar=no, menubar=no, resizable=no, height=500, width=700')"
      target="myiframe" data-toggle="modal" data-target="#myModal">
      <strong style="font-size:1.25em;">@{{location}}</strong><br />
      <strong>@{{type}}</strong> @{{id}} <em>@{{allocation_type}} </em>
    </a>
  </div>
  <div class="col-3">@{{value}}</div>
  <div class="col-1">

</div>
<div class="col-2">
@{{#categories}}
  <span  class="badge" style="background-color:@{{color}};color:@{{alt_color}}">
    @{{name}}
  </span>
  @{{/categories}}
</div>
<div class="col-2">


<!-- Default dropleft button -->
<div class="btn-group dropleft">
  <button type="button" class="btn btn-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    ...
  </button>
  <div class="dropdown-menu">
    <a class="dropdown-item" >See All @{{location}}</a>
<a class="dropdown-item" href="#">Delete</a>
  </div>
</div>
</div>
</div>
</script>

@push('scripts')
<script>
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

const search = instantsearch({

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
      header: 'Categories'
    }
  })
);

 search.addWidget(instantsearch.widgets.refinementList({
  container: '#ais-category',
  attributeName: 'category',
  autoHideContiner: false,
  collapsible:false,

}));


search.addWidget(instantsearch.widgets.refinementList({
 container: '#ais-payee',
 attributeName: 'payee',
     limit: 50,
 autoHideContiner: false,
 collapsible:false,

}));


search.start();

var transactions=[];

</script>
@endpush
@endsection
