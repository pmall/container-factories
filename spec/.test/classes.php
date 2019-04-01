<?php

namespace Test;

use Psr\Container\ContainerInterface;

function test_function () {
    //
}

interface TestInterface
{
    //
}

final class TestClass implements TestInterface
{
    private $xs;

    public function __construct(...$xs)
    {
        $this->xs = $xs;
    }
}

final class TestFactory
{
    public static function createStatic()
    {
        //
    }

    public function create()
    {
        //
    }

    public function __invoke(ContainerInterface $container)
    {
        //
    }
}

final class TestInvokable
{
    public function __invoke(ContainerInterface $container)
    {
        return $container->get('id');
    }
}
