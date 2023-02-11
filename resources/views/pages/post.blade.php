@extends('layouts.blog')

@section('content')

<article class="blog-post">
  <h2 class="blog-post-title mb-1 text-black no-underline">{{ $post->title }}</h2>
  <p class="blog-post-meta"><a href="#">{{ $post->user?->name ?? 'Anonymous' }}</a> @ {{ date('j.n.Y G:i', strtotime($post->created_at)) }}</p>
  <p>{!! nl2br(trim($post->content)) !!}</p>
</article>


@endsection
