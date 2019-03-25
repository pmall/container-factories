<?php declare(strict_types=1);

namespace Quanta\Container\Configuration\Passes;

final class InterfaceAliasingPass implements ProcessingPassInterface
{
    /**
     * @inheritdoc
     */
    public function aliases(string $id): array
    {
        try {
            $reflection = new \ReflectionClass($id);

            return ! $reflection->isInterface() && $reflection->isUserDefined()
                ? $reflection->getInterfaceNames()
                : [];
        }

        catch (\ReflectionException $e) {
            return [];
        }
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
