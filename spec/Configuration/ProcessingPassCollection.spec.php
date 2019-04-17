<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\MergedProcessingPass;
use Quanta\Container\ProcessingPassInterface;
use Quanta\Container\Configuration\ConfigurationUnit;
use Quanta\Container\Configuration\ConfigurationInterface;
use Quanta\Container\Configuration\ProcessingPassCollection;

describe('ProcessingPassCollection', function () {

    context('when there is no processing pass', function () {

        beforeEach(function () {

            $this->configuration = new ProcessingPassCollection;

        });

        it('should implement ConfigurationInterface', function () {

            expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

        });

        describe('->unit()', function () {

            it('should return a configuration unit with an empty merged processing pass', function () {

                $test = $this->configuration->unit();

                expect($test)->toEqual(new ConfigurationUnit([]));

            });

        });

    });

    context('when there is at least one processing pass', function () {

        beforeEach(function () {

            $this->pass1 = mock(ProcessingPassInterface::class);
            $this->pass2 = mock(ProcessingPassInterface::class);
            $this->pass3 = mock(ProcessingPassInterface::class);

            $this->configuration = new ProcessingPassCollection(...[
                $this->pass1->get(),
                $this->pass2->get(),
                $this->pass3->get(),
            ]);

        });

        it('should implement ConfigurationInterface', function () {

            expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

        });

        describe('->unit()', function () {

            it('should return a configuration unit with a merged processing pass', function () {

                $test = $this->configuration->unit();

                expect($test)->toEqual(new ConfigurationUnit([], ...[
                    $this->pass1->get(),
                    $this->pass2->get(),
                    $this->pass3->get(),
                ]));

            });

        });

    });

});
