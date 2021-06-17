@extends('layout')

@section('content')
    <h1>customers</h1>

    <ul>
        @foreach($customers as $customer)
        <li>{{$customer}}</li>

        @endforeach
    </ul>
@endsection