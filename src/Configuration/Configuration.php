<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

final class Configuration implements ConfigurationInterface
{
    /**
     * The associative array of factories.
     *
     * @var callable[]
     */
    private $factories;

    /**
     * The associative array of mappers.
     *
     * @var callable[]
     */
    private $mappers;

    /**
     * The associative array of extensions.
     *
     * @var callable[]
     */
    private $extensions;

    /**
     * Constructor.
     *
     * @param callable[] $factories
     * @param callable[] $mappers
     * @param callable[] $extensions
     * @throws \InvalidArgumentException
     */
    public function __construct(array $factories, array $mappers, array $extensions)
    {
        $result = \Quanta\ArrayTypeCheck::result($factories, 'callable');

        if (! $result->isValid()) {
            throw new \InvalidArgumentException(
                $result->message()->constructor($this, 1)
            );
        }

        $result = \Quanta\ArrayTypeCheck::result($mappers, 'callable');

        if (! $result->isValid()) {
            throw new \InvalidArgumentException(
                $result->message()->constructor($this, 2)
            );
        }

        $result = \Quanta\ArrayTypeCheck::result($extensions, 'callable');

        if (! $result->isValid()) {
            throw new \InvalidArgumentException(
                $result->message()->constructor($this, 3)
            );
        }

        $this->factories = $factories;
        $this->mappers = $mappers;
        $this->extensions = $extensions;
    }

    /**
     * @inheritdoc
     */
    public function factories(): array
    {
        return $this->factories;
    }

    /**
     * @inheritdoc
     */
    public function mappers(): array
    {
        return $this->mappers;
    }

    /**
     * @inheritdoc
     */
    public function extensions(string ...$ids): array
    {
        return array_map(function ($extension) {
            return [$extension];
        }, $this->extensions);
    }
}
