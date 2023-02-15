<article class="blog-post">
  @if ($post->image)
    <img class="object-cover w-full h-32" src="{{ $post->image }}" />
  @endif
  <h2 class="blog-post-title mb-1 text-black no-underline"><a class="no-underline text-black font-bold" href="/wiki/{{ $post->slug }}/">{{ $post->title }}</a></h2>
  <p><?php
    $parts = preg_split('/(\r?\n\r?\n)|(<\/p><p>)/', $post->content);
    echo count($parts) > 1 ? trim($parts[0]).sprintf('<br><br><a href="%s">%s...</a>', "/wiki/$post->slug/", __('Continue reading')) : $post->content;
  ?></p>
</article>
