<?php declare(strict_types=1);

namespace Quanta\Container\Passes;

final class DummyProcessingPass implements ProcessingPassInterface
{
    /**
     * @inheritdoc
     */
    public function aliases(string $id): array
    {
        return [];
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
