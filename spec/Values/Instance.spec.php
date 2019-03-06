<?php

use function Eloquent\Phony\Kahlan\mock;

use Psr\Container\ContainerInterface;

use Quanta\Container\Values\Instance;
use Quanta\Container\Values\ValueInterface;

require_once __DIR__ . '/../.test/classes.php';

describe('Instance', function () {

    context('when there is no argument', function () {

        beforeEach(function () {

            $this->value = new Instance(Test\TestClass::class);

        });

        it('should implement FactoryInterface', function () {

            expect($this->value)->toBeAnInstanceOf(ValueInterface::class);

        });

        describe('->value()', function () {

            it('should return an instance of the class with no argument', function () {

                $container = mock(ContainerInterface::class);

                $test = $this->value->value($container->get());

                expect($test)->toEqual(new Test\TestClass);

            });

        });

        describe('->str()', function () {

            it('should return a string representation of the class instantiation with no argument', function () {

                $test = $this->value->str('container');

                expect($test)->toEqual('new \Test\TestClass');

            });

        });

    });

    context('when there is at least one argument', function () {

        beforeEach(function () {

            $this->value1 = mock(ValueInterface::class);
            $this->value2 = mock(ValueInterface::class);
            $this->value3 = mock(ValueInterface::class);

            $this->value = new Instance(Test\TestClass::class, ...[
                $this->value1->get(),
                $this->value2->get(),
                $this->value3->get(),
            ]);

        });

        it('should implement FactoryInterface', function () {

            expect($this->value)->toBeAnInstanceOf(ValueInterface::class);

        });

        describe('->value()', function () {

            it('should return an instance of the class with the values returned by the ValueInterface implementations ->value() methods as arguments', function () {

                $container = mock(ContainerInterface::class);

                $this->value1->value->with($container)->returns('x1');
                $this->value2->value->with($container)->returns('x2');
                $this->value3->value->with($container)->returns('x3');

                $test = $this->value->value($container->get());

                expect($test)->toEqual(new Test\TestClass('x1', 'x2', 'x3'));

            });

        });

        describe('->str()', function () {

            it('should return a string representation of the class instantiation with the values returned by the ValueInterface implementations ->str() methods as arguments', function () {

                $this->value1->str->with('container')->returns('\'x1\'');
                $this->value2->str->with('container')->returns('\'x2\'');
                $this->value3->str->with('container')->returns('\'x3\'');

                $test = $this->value->str('container');

                expect($test)->toEqual(<<<'EOT'
new \Test\TestClass(
    'x1',
    'x2',
    'x3'
)
EOT
                );

            });

        });

    });

});
