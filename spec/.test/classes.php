<?php

namespace Test;

use Psr\Container\ContainerInterface;

use Quanta\Container\Configuration\Passes\ProcessingPassInterface;

interface TestInterface1
{
    //
}

interface TestInterface2
{
    //
}

interface TestInterface3
{
    //
}

final class TestClass implements TestInterface1, TestInterface2, TestInterface3
{
    private $xs;

    public function __construct(...$xs)
    {
        $this->xs = $xs;
    }
}

final class TestFactory
{
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function createStatic()
    {

    }

    public function create()
    {

    }

    public function __invoke(ContainerInterface $container)
    {
        //
    }
}

final class TestInvokable
{
    private static $container;

    private static $value;

    public static function setup(ContainerInterface $container, $value)
    {
        self::$container = $container;
        self::$value = $value;
    }

    public function __invoke(ContainerInterface $container)
    {
        if (self::$container === $container) {
            return self::$value;
        };
    }
}

final class TestProcessingPass implements ProcessingPassInterface
{
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function aliases(string $id): array
    {
        return [];
    }

    public function tags(string ...$ids): array
    {
        return [];
    }

    public function processed(string $id, callable $factory): callable
    {
        return $factory;
    }
}
