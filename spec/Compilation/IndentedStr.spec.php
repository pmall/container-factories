<?php

use Quanta\Container\Compilation\IndentedStr;

describe('IndentedStr', function () {

    beforeEach(function () {

        $this->str = implode(PHP_EOL, ['line1', 'line2', 'line3']);

    });

    context('when there is no number of spaces', function () {

        describe('->__toString()', function () {

            it('should return the string with each line prepended with 4 spaces', function () {

                expect((string) new IndentedStr($this->str))->toEqual(<<<'EOT'
    line1
    line2
    line3
EOT
                );

            });

        });

    });

    context('when there is no number of spaces', function () {

        describe('->__toString()', function () {

            it('should return the string with each line prepended with the number of spaces', function () {

                expect((string) new IndentedStr($this->str, 8))->toEqual(<<<'EOT'
        line1
        line2
        line3
EOT
                );

            });

        });

    });

});
