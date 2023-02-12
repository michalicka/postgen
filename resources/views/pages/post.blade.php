@extends('layouts.blog')

@section('content')

<article class="blog-post">
  @if ($post->image)
    <img class="object-cover w-full h-32" src="{{ $post->image }}" />
  @endif
  <h2 class="blog-post-title mb-1 text-black no-underline">{{ $post->title }}</h2>
  <p class="blog-post-meta">{{ __('Posted by') }} <a href="{{ $post->user_id ? '/author/'.$post->user_id : '#' }}">{{ $post->user?->name ?? 'Anonymous' }}</a> @ {{ date('j.n.Y G:i', strtotime($post->published_at)) }} {{ __('in category') }} <a href="{{ $post->category ? '/category/'.\Str::slug($post->category) : '#' }}">{{ $post->category }}</a></p>
  <p>{!! nl2br(trim($post->content)) !!}</p>
</article>


@endsection
