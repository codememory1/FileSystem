<?php

namespace Codememory\FileSystem;

/**
 * Class Reader
 * @package Codememory\FileSystem\src
 *
 * @author  Codememory
 */
class Reader extends DescriptorAbstract
{

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Read the entire file by passing $ length, you can output
     * the maximum number of bytes from the file
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param int|null $length
     *
     * @return string|null
     */
    public function read(?int $length = null): string|null
    {

        $mode = $this->mode;
        $fileSize = filesize($this->file->getRealPath($this->openFilePath));

        if ('Windows' === PHP_OS) {
            $mode .= 'b';
        }

        $this->open($this->openFilePath, $mode);

        $length = null === $length ? 0 === $fileSize ? 1 : $fileSize : $length;
        $read = fread($this->getDescriptor(), $length);

        $this->closeDescriptor();

        return $read ?: null;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Print an array of all lines from the file, the
     * key is the line number
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param bool $removeBlankLines
     *
     * @return array
     */
    public function onLines(bool $removeBlankLines = false): array
    {

        $arrayOfStrings = explode(PHP_EOL, $this->read());
        $lines = [];

        foreach ($arrayOfStrings as $index => $value) {
            $lines[++$index] = $value;
        }

        foreach ($lines as $index => $value) {
            if ($removeBlankLines && empty($value)) {
                unset($lines[$index]);
            }
        }

        return $lines;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Output content from file up to first occurrence
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $separator
     *
     * @return string
     */
    public function toFirstChar(string $separator): string
    {

        return mb_substr($this->read(), 0, mb_stripos($this->read(), $separator));

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Output content from file up to the last occurrence
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $separator
     *
     * @return string
     */
    public function toLastChar(string $separator): string
    {

        return mb_substr($this->read(), 0, mb_strripos($this->read(), $separator));

    }

}