<?php

namespace App\Services\Python;

use Exception;
use Illuminate\Support\Facades\Process;

class PythonService
{
    public string $venv;

    public function __construct()
    {
        $this->venv = 'source ' . base_path('venv\bin\activate');
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $this->venv = base_path('venv\Scripts\activate');
        }
    }
    
    /**
     * Run a Python script using the specified virtual environment and parameters.
     *
     * @param string $filename The name of the Python script file.
     * @param array $parameters An array of parameters to pass to the Python script.
     * @return string The output of the Python script after execution.
     * @throws \Exception If the execution of the Python script is not successful.
     */
    public function run(string $filename, array $parameters = [])
    {
        $process = Process::timeout(600)->run($this->venv . ' && python ' . $filename . ' ' . implode(' ', $parameters));
        if (!$process->successful()) {
            throw new Exception($process->errorOutput());
        }

        return rtrim($process->output(), "\n");
    }
}
