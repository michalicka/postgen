<?php

namespace Postgen\Api\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Routing\Controller;

class ImageApiController extends Controller
{
    public function preview()
    {
        $prompt = request('q');

        $client = new Client();
        $response = $client->get('https://pixabay.com/api/', [
            RequestOptions::QUERY => [
                'key' => env('PIXABAY_KEY'),
                'q' => $prompt,
                'lang' => env('LOCALE'),
                'image_type' => 'photo',
                'orientation' => 'horizontal',
                'min_width' => 640,
                'safesearch' => true,
                'per_page' => 50,
            ]
        ]);

        return response((string)$response->getBody());
    }

    public function generate()
    {
        $prompt = request('q');

        $client = new Client([
            RequestOptions::HEADERS => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.env('OPENAI_KEY'),
                'OpenAI-Organization' => env('OPENAI_ORG')
            ]
        ]);

        $response = $client->post('https://api.openai.com/v1/images/generations', [
            RequestOptions::JSON => [
                'prompt' => $prompt,
                'n' => 1,
                'size' => '1024x1024',
                'user' => hash('sha256', auth()->user()->email),
            ]
        ]);

        return response((string)$response->getBody());
    }
}
