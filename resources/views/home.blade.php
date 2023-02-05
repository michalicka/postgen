@extends('layouts.app')

@section('content')
<div>
    <app api-url="{{ $api_url }}" api-user="{{ $api_user }}"></app>
    <moderate></moderate>
</div>
@endsection
