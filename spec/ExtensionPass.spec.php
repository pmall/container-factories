<?php

use Quanta\Container\ExtensionPass;
use Quanta\Container\ProcessingPassInterface;
use Quanta\Container\Factories\Extension;

use Quanta\Exceptions\ArrayArgumentTypeErrorMessage;

describe('ExtensionPass', function () {

    context('when all the values of the associative array of factories are callable', function () {

        beforeEach(function () {

            $this->pass = new ExtensionPass([
                'id1' => $this->extension1 = function () {},
                'id3' => $this->extension3 = function () {},
                'id4' => $this->extension4 = function () {},
            ]);

        });

        it('should implement ProcessingPassInterface', function () {

            expect($this->pass)->toBeAnInstanceOf(ProcessingPassInterface::class);

        });

        context('->processed()', function () {

            it('should extends the given associative array of factories', function () {

                $test = $this->pass->processed([
                    'id1' => $factory1 = function () {},
                    'id2' => $factory2 = function () {},
                    'id3' => $factory3 = function () {},
                ]);

                expect($test['id1'])->toEqual(new Extension($factory1, $this->extension1));
                expect($test['id2'])->toEqual($factory2);
                expect($test['id3'])->toEqual(new Extension($factory3, $this->extension3));

            });

        });

    });

    context('when a value of the associative array of factories is not a callable', function () {

        it('should throw an InvalidArgumentException', function () {

            ArrayArgumentTypeErrorMessage::testing();

            $extensions = [
                'id1' => function () {},
                'id2' => 1,
                'id3' => function () {},
            ];

            $test = function () use ($extensions) {
                new ExtensionPass($extensions);
            };

            expect($test)->toThrow(new InvalidArgumentException(
                (string) new ArrayArgumentTypeErrorMessage(1, 'callable', $extensions)
            ));

        });

    });

});
