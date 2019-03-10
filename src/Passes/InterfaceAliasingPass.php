<?php declare(strict_types=1);

namespace Quanta\Container\Passes;

final class InterfaceAliasingPass implements ProcessingPassInterface
{
    /**
     * @inheritdoc
     */
    public function aliases(string $id): array
    {
        return class_exists($id) && ($interfaces = class_implements($id, true))
            ? array_values($interfaces)
            : [];
    }

    /**
     * @inheritdoc
     */
    public function tags(string ...$ids): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function processed(string $id, callable $factory): callable
    {
        return $factory;
    }
}
