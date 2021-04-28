<?php

namespace Codememory\FileSystem\Exceptions;

use ErrorException;
use JetBrains\PhpStorm\Pure;

/**
 * Class RootConstantNotFoundException
 * @package Codememory\FileSystem\src\Exceptions
 *
 * @author  Codememory
 */
class RootConstantNotFoundException extends ErrorException
{

    /**
     * RootConstantNotFoundException constructor.
     *
     * @param string $constantName
     */
    #[Pure] public function __construct(string $constantName)
    {

        parent::__construct(sprintf(
            'Create a %s constant in the file to which all requests go and pass the full path to the root as the value',
            $constantName
        ));

    }

}