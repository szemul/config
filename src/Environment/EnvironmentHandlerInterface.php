<?php
declare(strict_types=1);

namespace Szemul\Config\Environment;

use Szemul\Config\Exception\MissingEnvValueException;

interface EnvironmentHandlerInterface
{
    /**
     * Returns the value for the specified key from the loaded environments. If the key is not set in the
     * environments AND no default is provided it throws an exception. If a default is provided, it returns the default.
     *
     * @throws MissingEnvValueException
     */
    public function getValue(string $key, null|string|int|float|bool $default = null): null|string|int|float|bool;
}
