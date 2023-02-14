@extends('layouts.app')

@section('content')
<div id="app">
   <{{ $app }} :id="{{ $id ?? 0 }}" api-url="{{ $api_url }}" api-user="{{ $api_user }}"></{{ $app }}>
</div>
@endsection
