<?php

namespace Tallify;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class Filesystem
{
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
