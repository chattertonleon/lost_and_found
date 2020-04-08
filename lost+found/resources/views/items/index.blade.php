@extends('layouts.app')
@section('content')
<link rel="stylesheet" href="{{ asset('css/landingPage.css') }}">
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-8 ">
      <div class="card" id="navigation">
        <h1>Lost and Found</h1>
      </div>
      <div class="card">
        <div class="card-header">Lost items</div>
        @if ($errors->any())
        <div class="alert alert-danger">
          <ul> @foreach ($errors->all() as $error)
            <li>{{ $error }}</li> @endforeach
          </ul>
        </div><br />
        @endif
        <!-- display the success status -->
        @if (\Session::has('success'))
        <div class="alert alert-success">
          <p>{{ \Session::get('success') }}</p>
        </div><br />
        @endif
        <div class="card-body">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Category</th>
                <th>Color</th>
                <th>Date Lost</th>
              </tr>
            </thead>
            @guest
            <tbody>
              @foreach($items as $item)
              <tr>
                <td>{{$item['category']}}</td>
                <td>{{$item['color']}}</td>
                <td>{{$item['date_lost']}}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
          @else
          <tbody>
            @foreach($items as $item)
            <tr>
              <td>{{$item['category']}}</td>
              <td>{{$item['color']}}</td>
              <td>{{$item['date_lost']}}</td>
              <td>
                <form action="{{action('ItemsController@show',$item['id'])}}"method="post">
                  @csrf
                  <input name="_method" type="hidden" value="GET">
                  <button class="btn btn-primary" type="submit">More Info</button>
                </form>
              </td>
              <td>
                <form action="{{action('ClaimsController@edit',$item['id'])}}"method="post">
                  @csrf
                  <input name="_method" type="hidden" value="GET">
                  <button class="btn btn-primary" type="submit">Claim</button>
                </form>
              </td>
            </tr>
            @endforeach
          </tbody>
          @endguest
        </table>
      </div>
    </div>
  </div>
</div>
</div>
@endsection
