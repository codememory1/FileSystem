<?php

namespace Codememory\FileSystem;

use Codememory\FileSystem\Interfaces\FileInterface;

/**
 * Class Considered
 * @package Codememory\FileSystem\src
 *
 * @author  Codememory
 */
class Considered
{

    /**
     * @var FileInterface
     */
    private FileInterface $file;

    /**
     * Considered constructor.
     *
     * @param FileInterface $file
     */
    public function __construct(FileInterface $file)
    {

        $this->file = $file;

    }

    /**
     * @param array|string $path
     * @param string       $function
     *
     * @return bool
     */
    private function handlerConsidered(array|string $path, string $function): bool
    {

        if (is_string($path)) {
            return $function($this->file->getRealPath($path));
        }

        foreach ($path as $value) {
            if (!$this->handlerConsidered($value, $function)) {
                return false;
            }
        }

        return true;

    }


    /**
     * =>=>=>=>=>=>=>=>=>=>
     * Is or path a file
     * <=<=<=<=<=<=<=<=<=<=
     *
     * @param array|string $path
     *
     * @return bool
     */
    public function file(array|string $path): bool
    {

        return $this->handlerConsidered($path, 'is_file');

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Is or path a directory
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param array|string $path
     *
     * @return bool
     */
    public function directory(array|string $path): bool
    {

        return $this->handlerConsidered($path, 'is_dir');

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Is the path a file and is this file readable
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param array|string $path
     *
     * @return bool
     */
    public function read(array|string $path): bool
    {

        return $this->handlerConsidered($path, 'is_readable');

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Is the path a file and is it possible to write
     * content to this file
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param array|string $path
     *
     * @return bool
     */
    public function write(array|string $path): bool
    {

        return $this->handlerConsidered($path, 'is_write');

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>
     * Is the path a link
     * <=<=<=<=<=<=<=<=<=<=
     *
     * @param array|string $path
     *
     * @return bool
     */
    public function link(array|string $path): bool
    {

        return $this->handlerConsidered($path, 'is_link');

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Checking if the path is a file and if the end of the
     * file has been reached
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string        $path
     * @param callable|null $handler
     *
     * @return bool
     */
    public function fileEndReached(string $path, ?callable $handler = null): bool
    {

        if (!$this->file->exist($path) && !$this->read($path)) {
            return false;
        }

        $this->file->reader->open($path);
        $eof = feof($this->file->reader->getDescriptor());

        if (null !== $handler) {
            call_user_func_array($handler, [$eof, $this->file->reader->read()]);
        }

        return $eof;

    }

}