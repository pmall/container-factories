<?php

use Quanta\Container\Helpers\SelfExecutingClosureStr;

describe('SelfExecutingClosureStr', function () {

    describe('->__toString()', function () {

        it('should return a string representation of the self executing closure', function () {

            $test = (string) new SelfExecutingClosureStr('container', 'body');

            expect($test)->toEqual(<<<'EOT'
(function (\Psr\Container\ContainerInterface $container) {
    body
})($container)
EOT
            );

        });

    });

});
