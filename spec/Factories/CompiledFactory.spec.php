<?php

use Quanta\Container\Factories\CompiledFactory;

describe('CompiledFactory', function () {

    describe('->__toString()', function () {

        it('should return the string representation of a factory', function () {

            $test = (string) new CompiledFactory('container', '// body');

            expect($test)->toEqual(<<<'EOT'
function (\Psr\Container\ContainerInterface $container) {
    // body
}
EOT
            );

        });

    });

});
