<?php

use Quanta\Container\Compilation\SelfExecutingClosure;

describe('SelfExecutingClosure', function () {

    describe('->__toString()', function () {

        it('should return a string representation of the self executing closure', function () {

            $test = (string) new SelfExecutingClosure('container', 'body');

            expect($test)->toEqual(<<<'EOT'
(function (\Psr\Container\ContainerInterface $container) {
    body
})($container)
EOT
            );

        });

    });

});
