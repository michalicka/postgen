@extends('layouts.blog')

@section('content')

<article class="blog-post">
  @if ($post->image)
    <img class="w-full rounded mb-4" src="{{ $post->image }}" />
  @endif
  <h2 class="blog-post-title mb-1 text-black no-underline">{{ $post->title }}</h2>
  <p class="blog-post-meta">
    {{ __('Posted by') }}
    @if ($post->user_id)
        <a href="{{ '/author/'.$post->user_id }}">{{ $post->user->name }}</a>
    @else
        <strong>Anonymous</strong>
    @endif
    @ {{ date('j.n.Y G:i', strtotime($post->published_at)) }}
    @if ($post->category)
        {{ __('in category') }} <a href="{{ $post->category ? '/category/'.\Str::slug($post->category) : '#' }}">{{ $post->category }}</a>
    @endif
  </p>
  <p>{!! nl2br(trim($post->content)) !!}</p>
  @if (count($post->tags))
      <div class="text-xs flex flex-wrap gap-2 mt-2 pt-2 items-center border-t border-gray-300">
        <div class="font-bold">{{ __('Tags') }}:</div>
        @foreach ($post->tags as $tag)
            @if (\Str::slug($tag) === $tag)
                <a href="/tag/{{ \Str::slug($tag) }}" class="bg-gray-100 rounded-md px-2 py-1 no-underline text-gray-700">{{ $tag }}</a>
            @endif
        @endforeach
      </div>
  @endif
</article>


@endsection
