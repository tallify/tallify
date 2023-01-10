<?php

namespace Tallify;

use RuntimeException;
use function Termwind\{render};
use Symfony\Component\Process\Process;

class Command
{
    /**
     * Run the given commands.
     *
     * @param  array  $commands
     * @param  array  $env
     * @return \Symfony\Component\Process\Process
     */
    public function runCommands($commands, array $env = [])
    {
        $process = Process::fromShellCommandline(implode(' && ', $commands), null, $env, null, null);

        if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
            try {
                $process->setTty(true);
            } catch (RuntimeException $e) {
                render('<div class="mb-1"><div class="px-1 font-bold bg-orange-600">WARNING !</div><em class="ml-1">' . $e->getMessage() . '</em></div>');
            }
        }

        $process->run(function ($type, $line) {
            render('<div class="mb-1 bg-green-700">     ' . $line . '</div>');
        });

        return $process;
    }

    /**
     * cUrl the given url and returns its status code.
     *
     * @param  string  $url
     * @return @statusCode
     */
    public function cUrl($url)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_exec($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        return $statusCode;
    }
}
