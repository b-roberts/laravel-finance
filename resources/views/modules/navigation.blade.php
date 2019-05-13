<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="#">OPFT</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
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
        <a class="nav-link" href="{{route('rule.index')}}">Rules</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{route('settings')}}">Settings</a>
      </li>
    </ul>
  </div>
</nav>
