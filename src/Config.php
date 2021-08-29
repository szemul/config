<?php
declare(strict_types=1);

namespace Szemul\Config;

use InvalidArgumentException;
use Szemul\Config\Exception\MissingConfigValueException;

class Config implements ConfigInterface
{
    /** @var array<string,mixed> */
    protected array $configData = [];

    public function get(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, $this->configData)) {
            return $this->configData[$key];
        }

        if (func_num_args() >= 2) {
            return $default;
        }

        throw new MissingConfigValueException("The config key '$key' doesn't exist");
    }

    public function getPrefix(string $keyPrefix): array
    {
        $keyPrefix = rtrim($keyPrefix, '.');

        if (empty($keyPrefix)) {
            throw new InvalidArgumentException('The key prefix can not be empty');
        }

        $keyPrefixWithDot = $keyPrefix . '.';
        $results          = [];

        foreach ($this->configData as $key => $value) {
            if (str_starts_with($key, $keyPrefixWithDot)) {
                $keyWithoutPrefix = preg_replace('/^' . preg_quote($keyPrefixWithDot, '/') . '/', '', $key);

                $results[$keyWithoutPrefix] = $value;
            }
        }

        if (empty($results)) {
            throw new MissingConfigValueException("The config key prefix $keyPrefix doesn't exist");
        }

        return $results;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->configData);
    }

    public function hasPrefix(string $keyPrefix): bool
    {
        $keyPrefix = rtrim($keyPrefix, '.');

        if (empty($keyPrefix)) {
            throw new InvalidArgumentException('The key prefix can not be empty');
        }

        $keyPrefixWithDot = $keyPrefix . '.';

        foreach ($this->configData as $key => $value) {
            if (str_starts_with($key, $keyPrefixWithDot)) {
                return true;
            }
        }

        return false;
    }

    public function set(string $key, mixed $value): static
    {
        $this->configData[$key] = $value;

        return $this;
    }

    public function setArray(array $data): static
    {
        $this->configData = array_merge($this->configData, $data);

        return $this;
    }

    public function toArray(): array
    {
        return $this->configData;
    }
}
