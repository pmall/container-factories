<?php

use Quanta\Container\Helpers\LinesStr;

describe('LinesStr', function () {

    context('when there is no line', function () {

        describe('->toString()', function () {

            it('should return an empty string', function () {

                $test = (string) new LinesStr;

                expect($test)->toEqual('');

            });

        });

    });

    context('when there is one line', function () {

        describe('->toString()', function () {

            it('should return the line', function () {

                $test = (string) new LinesStr('line');

                expect($test)->toEqual('line');

            });

        });

    });

    context('when there more than one line', function () {

        describe('->toString()', function () {

            it('should return the lines spaced by a comma and a new line', function () {

                $test = (string) new LinesStr('line1', 'line2', 'line3');

                expect($test)->toEqual(<<<'EOT'
line1,
line2,
line3
EOT
                );

            });

        });

    });

});
