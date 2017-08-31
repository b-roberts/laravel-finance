<nav aria-label="Page navigation example">
  <ul class="pagination justify-content-center">
    <li class="page-item">
      <a class="page-link" href="{{route( \Route::currentRouteName(),["startDate"=>$startDate->copy()->subMonth()->toDateString()])}}" aria-label="Previous">
        <span aria-hidden="true">&laquo;</span>
        <span class="sr-only">Previous</span>
      </a>
    </li>
    @for($i=-3;$i<=3;$i++)
      @php
        $date = $startDate->copy()->addMonth($i);
      @endphp
      @if($i==0)
        <li class="page-item active">
      @else
        <li class="page-item">
      @endif
        <a class="page-link" href="{{route( \Route::currentRouteName(),["startDate"=>$date->toDateString()])}}">{{$date->format('M y')}}</a>
      </li>
    @endfor
    <li class="page-item">
      <a class="page-link" href="{{route( \Route::currentRouteName(),["startDate"=>$startDate->copy()->addMonth()->toDateString()])}}" aria-label="Next">
        <span aria-hidden="true">&raquo;</span>
        <span class="sr-only">Next</span>
      </a>
    </li>
  </ul>
</nav>
