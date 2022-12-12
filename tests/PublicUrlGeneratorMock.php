<?php

/*
 * This file is part of the flysystem-bundle project.
 *
 * (c) Titouan Galopin <galopintitouan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\League\FlysystemBundle;

use League\Flysystem\Config;
use League\Flysystem\UrlGeneration\PublicUrlGenerator;

final class PublicUrlGeneratorMock implements PublicUrlGenerator
{
    public function publicUrl(string $path, Config $config): string
    {
        return "https://example.org/generator/$path";
    }
}
