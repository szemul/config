<?php
declare(strict_types=1);

namespace Szemul\Config\Builder;

use Szemul\Config\ConfigInterface;
use Szemul\Config\Environment\EnvironmentHandlerInterface;
use Szemul\Config\Exception\MissingEnvValueException;

interface ConfigBuilderInterface
{
    /**
     * @throws MissingEnvValueException
     */
    public function build(EnvironmentHandlerInterface $environmentHandler, ConfigInterface $config): void;
}
