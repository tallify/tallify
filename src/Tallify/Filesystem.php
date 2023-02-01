<?php

namespace Tallify;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;

class Filesystem
{
    /**
     * Add the given string to the end of the given file.
     *
     * @param  string  $text
     * @param  string  $file
     * @return void
     */
    public function addInFile(string $text, string $file)
    {
        file_put_contents(
            $file,
            $text . "\n",
            FILE_APPEND | LOCK_EX
        );
    }

    /**
     * Append the contents to the given file.
     *
     * @param  string  $path
     * @param  string  $contents
     * @param  string|null  $owner
     * @return void
     */
    public function append($path, $contents, $owner = null)
    {
        file_put_contents($path, $contents, FILE_APPEND);

        if ($owner) {
            $this->chown($path, $owner);
        }
    }

    /**
     * Append the contents to the given file as the non-root user.
     *
     * @param  string  $path
     * @param  string  $contents
     * @return void
     */
    public function appendAsUser($path, $contents)
    {
        $this->append($path, $contents, user());
    }

    /**
     * Change the owner of the given path.
     *
     * @param  string  $path
     * @param  string  $user
     */
    public function chown($path, $user)
    {
        chown($path, $user);
    }

    /**
     * Copy the given file to a new location.
     *
     * @param  string  $from
     * @param  string  $to
     * @return void
     */
    public function copy($from, $to)
    {
        copy($from, $to);
    }

    /**
     * Copy the given file to a new location for the non-root user.
     *
     * @param  string  $from
     * @param  string  $to
     * @return void
     */
    public function copyAsUser($from, $to)
    {
        copy($from, $to);

        $this->chown($to, user());
    }

    /**
     * Ensure that the given directory exists.
     *
     * @param  string  $path
     * @param  string|null  $owner
     * @param  int  $mode
     * @return void
     */
    public function ensureDirExists($path, $owner = null, $mode = 0755)
    {
        if (!$this->isDir($path)) {
            $this->mkdir($path, $owner, $mode);
        }
    }

    /**
     * Determine if the given file exists.
     *
     * @param  string  $path
     * @return bool
     */
    public function exists($path)
    {
        return file_exists($path);
    }

    /**
     * Determine if the given path is a directory.
     *
     * @param  string  $path
     * @return bool
     */
    public function isDir($path)
    {
        return is_dir($path);
    }

    /**
     * Read the contents of the given file.
     *
     * @param  string  $path
     * @return string
     */
    public function get($path)
    {
        return file_get_contents($path);
    }

    /**
     * Get custom stub file if exists.
     *
     * @param  string  $filename
     * @return string
     */
    public function getStub($filename)
    {
        $default = __DIR__ . '/../stubs/' . $filename;

        $custom = TALLIFY_HOME_PATH . '/stubs/' . $filename;

        $path = file_exists($custom) ? $custom : $default;

        return $this->get($path);
    }

    /**
     * Copy the given folder to a new location.
     *
     * @param  string  $from
     * @param  string  $to
     * @return void
     */
    public function mirror($from, $to)
    {
        $fileSystem = new SymfonyFilesystem();

        $fileSystem->mirror($from, $to);
    }

    /**
     * Copy the given folder to a new location.
     *
     * @param  string  $from
     * @param  string  $to
     * @return void
     */
    public function mirrorAsUser($from, $to)
    {
        $fileSystem = new SymfonyFilesystem();

        $fileSystem->mirror($from, $to);

        $this->chown($to, user());
    }

    /**
     * Create a directory.
     *
     * @param  string  $path
     * @param  string|null  $owner
     * @param  int  $mode
     * @return void
     */
    public function mkdir($path, $owner = null, $mode = 0755)
    {
        mkdir($path, $mode, true);

        if ($owner) {
            $this->chown($path, $owner);
        }
    }

    /**
     * Write to the given file.
     *
     * @param  string  $path
     * @param  string  $contents
     * @param  string|null  $owner
     * @return void
     */
    public function put($path, $contents, $owner = null)
    {
        file_put_contents($path, $contents);

        if ($owner) {
            $this->chown($path, $owner);
        }
    }

    /**
     * Write to the given file as the non-root user.
     *
     * @param  string  $path
     * @param  string  $contents
     * @return void
     */
    public function putAsUser($path, $contents)
    {
        $this->put($path, $contents, user());
    }

    /**
     * Replace the given string in the given file.
     *
     * @param  string  $search
     * @param  string  $replace
     * @param  string  $file
     * @return void
     */
    public function replaceInFile(string $search, string $replace, string $file)
    {
        file_put_contents(
            $file,
            str_replace($search, $replace, file_get_contents($file))
        );

        $this->chown($file, user());
    }

    /**
     * Recursively delete a directory and its contents.
     *
     * @param  string  $path
     * @return void
     */
    public function rmDirAndContents($path)
    {
        $dir = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($dir, RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($files as $file) {
            $file->isDir() ? rmdir($file->getRealPath()) : unlink($file->getRealPath());
        }

        rmdir($path);
    }

    /**
     * Scan the given directory path.
     *
     * @param  string  $path
     * @return array
     */
    public function scandir($path)
    {
        return collect(scandir($path))
            ->reject(function ($file) {
                return in_array($file, ['.', '..']);
            })->values()->all();
    }
}
