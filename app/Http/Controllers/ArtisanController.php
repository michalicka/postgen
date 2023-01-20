<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Output\StreamOutput;

class ArtisanController extends Controller
{
    private $previousErrorHandler = false;

    public function errorHandler($errno, $errstr, $errfile, $errline)
    {
        if ($errno == 2 && preg_match('/putenv\(\)/i', $errstr)) {
            return true;
        }
        if ($this->previousErrorHandler != null) {
            return call_user_func($this->previousErrorHandler, $errno, $errstr, $errfile, $errline);
        }
        return false;
    }

    protected function callArtisan($command, $parameters = [])
    {
        if ($this->previousErrorHandler === false) {
            $this->previousErrorHandler = set_error_handler( [$this, 'errorHandler']);
        }

        $outputStream = fopen('php://output', 'w');
        $output = new StreamOutput($outputStream);

        ob_start();
        echo "Command: $command\n---------\n";
        $exit = Artisan::call($command, $parameters, $output);
        echo "Exit code: ".intval($exit);
        echo PHP_EOL.PHP_EOL;

        $outputText = ob_get_clean();
        fclose($outputStream);

        return $outputText;
    }
    
    public function down()
    {
        $outputText = $this->callArtisan("down", [ '--refresh' => 15, '--retry' => 60 ]);

        return response($outputText, 200)
                  ->header('Content-Type', 'text/plain');
    }

    public function deploy()
    {
        $outputText = $this->callArtisan("view:clear");
        $outputText .= $this->callArtisan("view:cache");
        $outputText .= $this->callArtisan("config:cache");
        $outputText .= $this->callArtisan("route:cache");
        $outputText .= $this->callArtisan("migrate");
        $outputText .= $this->callArtisan("up");

        return response($outputText, 200)
                  ->header('Content-Type', 'text/plain');
    }
}
