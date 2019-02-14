<?php

use function Eloquent\Phony\Kahlan\mock;

use Psr\Container\ContainerInterface;

use Quanta\Container\Factories\Alias;
use Quanta\Container\Factories\CompilableFactoryInterface;

use Quanta\Container\Compilation\Template;

describe('Alias', function () {

    beforeEach(function () {

        $this->factory = new Alias('id');

    });

    it('should implement CompilableFactoryInterface', function () {

        expect($this->factory)->toBeAnInstanceOf(CompilableFactoryInterface::class);

    });

    describe('->__invoke()', function () {

        it('should return the container entry associated with the id', function () {

            $container = mock(ContainerInterface::class);

            $container->get->with('id')->returns('value');

            $test = ($this->factory)($container->get());

            expect($test)->toEqual('value');

        });

    });

    describe('->compiled()', function () {

        it('should return a string representation of the alias', function () {

            $template = Template::withDummyClosureCompiler('container_var_name');

            $test = $this->factory->compiled($template);

            expect($test)->toEqual(<<<'EOT'
function (\Psr\Container\ContainerInterface $container_var_name) {
    return $container_var_name->get('id');
}
EOT
            );

        });

    });

});
