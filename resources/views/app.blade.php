@extends('layouts.app')

@section('content')
<div id="app">
   <{{ $app }} :id="{{ $id ?? 0 }}"></{{ $app }}>
</div>
@endsection
