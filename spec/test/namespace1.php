<?php

namespace Test1;

use Interop\Container\ServiceProviderInterface;

final class TestClass1 {}

final class TestClass2 {}

final class ServiceProvider1 implements ServiceProviderInterface
{
    public function getFactories() {}
    public function getExtensions() {}
}

final class ServiceProvider2 implements ServiceProviderInterface
{
    public function getFactories() {}
    public function getExtensions() {}
}

final class ServiceProvider3 implements ServiceProviderInterface
{
    public function getFactories() {}
    public function getExtensions() {}
}
