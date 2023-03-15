<?php

namespace Postgen\Api\Controllers;

use Faker;
use Illuminate\Routing\Controller;

class GptApiController extends Controller
{
    public function faker()
    {
        return response()->stream(function () {
            $faker = Faker\Factory::create();
            $delta = str_split($faker->text, 1);

            while (count($delta)) {
                $data = json_encode([
                    'choices' => [
                        [
                            'finish_reason' => null,
                            'delta' => [
                                'content' => array_shift($delta)
                            ]
                        ]
                    ],
                    'model' => 'text-davinci-003'
                ]);

                echo "data: {$data}\n\n";

                ob_flush();
                flush();

                if (connection_aborted()) break;
                usleep(50000);
            }

            echo "data: [DONE]\n\n";

            ob_flush();
            flush();
        }, 200, [
            'Cache-Control' => 'no-cache',
            'Content-Type' => 'text/event-stream',
        ]);
    }
}
