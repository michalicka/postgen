<?php

namespace Postgen\Api\Controllers;

use Faker;
use Illuminate\Routing\Controller;

class FakerController extends Controller
{
    public function index()
    {
        $faker = Faker\Factory::create();

        $result = collect(range(0, 4))
            ->map(fn($id) => json_encode([
                'choices' => [
                    [
                        'finish_reason' => null,
                        'text' => $faker->text."\n\n"
                    ]
                ],
                'model' => 'text-davinci-003'
            ]))
            ->join("\ndata: ");

        $end = json_encode([
            'choices' => [
                [
                    'finish_reason' => true,
                    'text' => ''
                ]
            ],
            'model' => 'text-davinci-003'
        ]);

        return response("data: $result\ndata: $end");
    }
}
