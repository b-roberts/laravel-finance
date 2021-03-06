<nav class="navbar navbar-toggleable-md navbar-light bg-faded">
  <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <a class="navbar-brand" href="#">OPFT</a>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" href="{{route('transaction.index')}}">Transactions</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{route('account.index')}}">Accounts</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{route('cashflow')}}">Cashflow</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{route('income-statement')}}">Income Statement</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{route('settings')}}">Settings</a>
      </li>
    </ul>
  </div>
</nav>
