<?php

namespace Postgen\Common\Logic;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;

class Images
{
    public static function store(string $url): string
    {
        $client = new Client();
        $response = $client->get($url);
        $body = (string)$response->getBody();
        $filename = sprintf("%s.%s", sha1($body), pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg');
        Storage::disk('public')->put($filename, $body);

        return Storage::url($filename);
    }
}
