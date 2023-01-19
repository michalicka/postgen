@extends('layouts.app')

@section('content')
<div id="app">
   <app api-url="{{ $api_url }}" api-user="{{ $api_user }}"></app>
</div>
@endsection
