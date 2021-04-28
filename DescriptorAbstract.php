<?php

namespace Codememory\FileSystem;

use Codememory\FileSystem\Interfaces\FileInterface;

/**
 * Class DescriptorAbstract
 * @package Codememory\FileSystem\src
 *
 * @author  Codememory
 */
abstract class DescriptorAbstract
{

    /**
     * @var FileInterface
     */
    protected FileInterface $file;

    /**
     * @var resource
     */
    protected mixed $descriptor;

    /**
     * @var ?string
     */
    protected ?string $openFilePath = null;

    /**
     * @var ?string
     */
    protected ?string $mode = null;

    /**
     * Reader constructor.
     *
     * @param FileInterface $file
     */
    public function __construct(FileInterface $file)
    {

        $this->file = $file;

    }

    /**
     * @param string $filename
     * @param string $mode
     * @param bool   $createFile
     *
     * @return $this
     */
    public function open(string $filename, string $mode = 'r', bool $createFile = false): DescriptorAbstract
    {

        if (!$this->file->exist($filename) && $createFile) {
            file_put_contents($this->file->getRealPath($filename), null);

            $this->file->setPermission($filename);
        }

        $this->openFilePath = $filename;
        $this->mode = $mode;
        $this->descriptor = fopen($this->file->getRealPath($filename), $mode);

        return $this;

    }

    /**
     * @return resource
     */
    public function getDescriptor(): mixed
    {

        return $this->descriptor;

    }

    /**
     * @return bool
     */
    public function closeDescriptor(): bool
    {

        return fclose($this->getDescriptor());

    }

}