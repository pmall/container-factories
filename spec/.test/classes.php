<?php

namespace Test;

use Psr\Container\ContainerInterface;

final class TestInstance
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
