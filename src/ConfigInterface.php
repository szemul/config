<?php
declare(strict_types=1);

namespace Szemul\Config;

use InvalidArgumentException;
use Szemul\Config\Exception\MissingConfigValueException;

interface ConfigInterface
{
    /**
     * @throws MissingConfigValueException If the key is not set and there is no default
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * @return array<string,mixed>
     *
     * @throws InvalidArgumentException If the key is empty
     * @throws MissingConfigValueException If there are no matches to the prefix
     */
    public function getPrefix(string $keyPrefix): array;

    public function has(string $key): bool;

    /**
     * @throws InvalidArgumentException If the key is empty
     */
    public function hasPrefix(string $keyPrefix): bool;

    public function set(string $key, mixed $value): static;

    /**
     * @param array<string,mixed> $data
     */
    public function setArray(array $data): static;
}
