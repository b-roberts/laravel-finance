@extends('templates.narrow')
@section('content')
  <h1>Categories</h1>
  <table class="table table-striped">

  @foreach($categories as $category)
  <tr>
    <td>{{$category->name}}</td>
    <td>{{$category->designation->name}}</td>
    <td><a href="{{route('category.show',$category->id)}}" class="btn btn-secondary">View</a></td>
  </tr>
  @endforeach
  </table>

@endsection
