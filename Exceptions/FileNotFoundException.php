<?php

namespace Codememory\FileSystem\Exceptions;

use JetBrains\PhpStorm\Pure;

/**
 * Class FileNotFoundException
 * @package Codememory\FileSystem\Exceptions
 *
 * @author  Codememory
 */
class FileNotFoundException extends FileSystemException
{

    /**
     * FileNotFoundException constructor.
     *
     * @param string $path
     */
    #[Pure]
    public function __construct(string $path)
    {

        parent::__construct(sprintf(
            'The %s file does not exist in the %s directory, the full path is %s',
            basename($path),
            dirname($path),
            $path
        ));

    }

}