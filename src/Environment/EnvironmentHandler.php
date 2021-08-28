<?php
declare(strict_types=1);

namespace Szemul\Config\Environment;

use InvalidArgumentException;
use josegonzalez\Dotenv\Loader;
use Szemul\Config\Exception\MissingEnvValueException;

class EnvironmentHandler implements EnvironmentHandlerInterface
{
    /** @var string[] */
    protected array  $paths;
    /** @var array<string,string> */
    protected array  $envData;
    /** @var array<string,null|string|bool|int|float>|null */
    protected ?array $loadedData = null;
    protected Loader $loader;

    public function __construct(string ...$paths)
    {
        if (empty($paths)) {
            throw new InvalidArgumentException('No paths specified');
        }

        $this->envData = getenv();
        $this->paths   = $paths;
        $this->loader  = new Loader();
    }

    public function setLoader(Loader $loader): void
    {
        $this->loader = $loader;
    }

    /** @param array<string,string> $envData */
    public function setEnvData(array $envData): void
    {
        $this->envData = $envData;
    }

    protected function loadData(): void
    {
        $this->loadedData = array_merge(
            $this->loader->load(['filepath' => $this->paths])->toArray() ?? [],
            $this->envData,
        );
    }

    public function getValue(string $key, null|string|int|float|bool $default = null): null|string|int|float|bool
    {
        if (null === $this->loadedData) {
            $this->loadData();
        }

        if (!array_key_exists($key, $this->loadedData)) {
            if (func_num_args() < 2) {
                throw new MissingEnvValueException("The environment value $key was requested, but it was not set");
            }

            return $default;
        }

        return $this->loadedData[$key];
    }
}
