<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\EmptyFactoryMap;
use Quanta\Container\Configuration\Configuration;
use Quanta\Container\Configuration\ProcessingPassCollection;
use Quanta\Container\Configuration\ConfigurationSourceInterface;
use Quanta\Container\Configuration\Passes\MergedProcessingPass;
use Quanta\Container\Configuration\Passes\ProcessingPassInterface;

describe('ProcessingPassCollection', function () {

    context('when there is no processing pass', function () {

        beforeEach(function () {

            $this->source = new ProcessingPassCollection;

        });

        it('should implement ConfigurationSourceInterface', function () {

            expect($this->source)->toBeAnInstanceOf(ConfigurationSourceInterface::class);

        });

        describe('->configuration()', function () {

            it('should return a configuration with an empty merged processing pass', function () {

                $test = $this->source->configuration();

                expect($test)->toEqual(new Configuration(
                    new EmptyFactoryMap,
                    new MergedProcessingPass
                ));

            });

        });

    });

    context('when there is no processing pass', function () {

        beforeEach(function () {

            $this->pass1 = mock(ProcessingPassInterface::class);
            $this->pass2 = mock(ProcessingPassInterface::class);
            $this->pass3 = mock(ProcessingPassInterface::class);

            $this->source = new ProcessingPassCollection(...[
                $this->pass1->get(),
                $this->pass2->get(),
                $this->pass3->get(),
            ]);

        });

        it('should implement ConfigurationSourceInterface', function () {

            expect($this->source)->toBeAnInstanceOf(ConfigurationSourceInterface::class);

        });

        describe('->configuration()', function () {

            it('should return a configuration with a merged processing pass', function () {

                $test = $this->source->configuration();

                expect($test)->toEqual(new Configuration(
                    new EmptyFactoryMap,
                    new MergedProcessingPass(...[
                        $this->pass1->get(),
                        $this->pass2->get(),
                        $this->pass3->get(),
                    ])
                ));

            });

        });

    });

});
