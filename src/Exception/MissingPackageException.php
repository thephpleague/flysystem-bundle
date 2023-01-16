<?php

/*
 * This file is part of the flysystem-bundle project.
 *
 * (c) Titouan Galopin <galopintitouan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\FlysystemBundle\Exception;

/**
 * @author Titouan Galopin <galopintitouan@gmail.com>
 *
 * @final
 */
class MissingPackageException extends \RuntimeException
{
    public function __construct($message = '', \Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
