<?php

use Quanta\Container\Factories\CompiledFactory;

describe('CompiledFactory', function () {

    context('when there is no previous parameter declaration', function () {

        describe('->__toString()', function () {

            it('should return the string representation of a factory', function () {

                $test = (string) new CompiledFactory('container', '', '// body');

                expect($test)->toEqual(<<<'EOT'
function (\Psr\Container\ContainerInterface $container) {
    // body
}
EOT
                );

            });

        });

    });

    context('when there is a previous parameter declaration', function () {

        describe('->__toString()', function () {

            it('should return the string representation of a factory with a previous parameter', function () {

                $test = (string) new CompiledFactory('container', '$previous', '// body');

                expect($test)->toEqual(<<<'EOT'
function (\Psr\Container\ContainerInterface $container, $previous) {
    // body
}
EOT
                );

            });

        });

    });

});
