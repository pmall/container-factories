<?php declare(strict_types=1);

namespace Quanta\Container\Maps;

final class FactoryMap implements FactoryMapInterface
{
    /**
     * The associative array of factories.
     *
     * @var callable[]
     */
    private $factories;

    /**
     * Constructor.
     *
     * @param callable[] $factories
     * @throws \InvalidArgumentException
     */
    public function __construct(array $factories)
    {
        $result = \Quanta\ArrayTypeCheck::result($factories, 'callable');

        if (! $result->isValid()) {
            throw new \InvalidArgumentException(
                $result->message()->constructor($this, 1)
            );
        }

        $this->factories = $factories;
    }

    /**
     * @inheritdoc
     */
    public function factories(): array
    {
        return $this->factories;
    }
}
