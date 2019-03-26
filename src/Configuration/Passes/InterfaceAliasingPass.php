<?php declare(strict_types=1);

namespace Quanta\Container\Configuration\Passes;

use Quanta\Container\Utils;

final class InterfaceAliasingPass implements ProcessingPassInterface
{
    /**
     * @inheritdoc
     */
    public function aliases(string $id): array
    {
        try {
            $reflection = new \ReflectionClass($id);
        }

        catch (\ReflectionException $e) {
            return [];
        }

        if ($reflection->isInterface()) {
            return [];
        }

        $interfaces = array_values(
            array_filter($reflection->getInterfaces(), [$this, 'isUserDefined'])
        );

        return Utils::plucked($interfaces, 'getName');
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

    /**
     * Return whether the given reflection is reflecting an user defined class.
     *
     * @param \ReflectionClass $reflection
     * @return bool
     */
    private function isUserDefined(\ReflectionClass $reflection)
    {
        return $reflection->isUserDefined();
    }
}
