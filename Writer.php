<?php

namespace Codememory\FileSystem;

/**
 * Class Writer
 * @package Codememory\FileSystem\src
 *
 * @author  Codememory
 */
class Writer extends DescriptorAbstract
{

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Write content to the file, if there is something in the file,
     * then the content will be overwritten with a new one
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string   $content
     * @param int|null $length
     *
     * @return bool
     */
    public function put(string $content, ?int $length = null): bool
    {

        if (null !== $length) {
            $content = mb_substr($content, 0, $length);
        }

        return file_put_contents($this->file->getRealPath($this->openFilePath), $content);

    }

}