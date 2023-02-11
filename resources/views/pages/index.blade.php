@extends('layouts.blog')

@section('content')

  @each('pages.article', $posts, 'post')

  {{ $posts->links() }}

@endsection
