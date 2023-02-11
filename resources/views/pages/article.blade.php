<article class="blog-post">
  <h2 class="blog-post-title mb-1 text-black no-underline"><a class="no-underline text-black font-bold" href="/wiki/{{ $post->slug }}/">{{ $post->title }}</a></h2>
  <p class="blog-post-meta"><a href="{{ $post->user ? "/author/".$post->user->id : "#" }}">{{ $post->user?->name ?? 'Anonymous' }}</a> @ {{ date('j.n.Y G:i', strtotime($post->created_at)) }}</p>
  <p><?php
    $parts = preg_split('/\r?\n\r?\n/', $post->content);
    echo count($parts) > 1 ? trim($parts[0]).sprintf('<br><br><a href="%s">%s...</a>', "/wiki/$post->slug/", __('Continue reading')) : $post->content;
  ?></p>
</article>
