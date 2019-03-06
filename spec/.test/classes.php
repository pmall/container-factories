<?php

namespace Test;

use Psr\Container\ContainerInterface;

interface TestInterface
{
    //
}

final class TestInstance implements TestInterface
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
