<?php

use Quanta\Container\Factories\Extension;
use Quanta\Container\Configuration\ExtensionPass;
use Quanta\Container\Configuration\ConfigurationPassInterface;

use Quanta\Exceptions\ArrayArgumentTypeErrorMessage;

describe('ExtensionPass', function () {

    context('when all the values of the associative array of factories are callable', function () {

        beforeEach(function () {

            $this->pass = new ExtensionPass([
                'id1' => $this->extension1 = function () {},
                'id2' => $this->extension2 = function () {},
                'id3' => $this->extension3 = function () {},
            ]);

        });

        it('should implement ConfigurationPassInterface', function () {

            expect($this->pass)->toBeAnInstanceOf(ConfigurationPassInterface::class);

        });

        context('->processed()', function () {

            it('should extends the given associative array of factories', function () {

                $test = $this->pass->processed([
                    'id1' => $factory1 = function () {},
                    'id3' => $factory3 = function () {},
                    'id4' => $factory4 = function () {},
                ]);

                expect($test['id1'])->toEqual(new Extension($factory1, $this->extension1));
                expect($test['id2'])->toEqual($this->extension2);
                expect($test['id3'])->toEqual(new Extension($factory3, $this->extension3));
                expect($test['id4'])->toEqual($factory4);

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
