<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\Configuration;
use Quanta\Container\FactoryMapInterface;
use Quanta\Container\ConfigurationInterface;
use Quanta\Container\ConfigurationPassInterface;

describe('Configuration', function () {

    beforeEach(function () {

        $this->map = mock(FactoryMapInterface::class);

    });

    context('when there is no configuration passes', function () {

        beforeEach(function () {

            $this->configuration = new Configuration($this->map->get());

        });

        it('should implement ConfigurationInterface', function () {

            expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

        });

        describe('->map()', function () {

            it('should return the factory map', function () {

                $test = $this->configuration->map();

                expect($test)->toBe($this->map->get());

            });

        });

        describe('->passes()', function () {

            it('should return an empty array', function () {

                $test = $this->configuration->passes();

                expect($test)->toEqual([]);

            });

        });

    });

    context('when there is configuration passes', function () {

        beforeEach(function () {

            $this->pass1 = mock(ConfigurationPassInterface::class);
            $this->pass2 = mock(ConfigurationPassInterface::class);
            $this->pass3 = mock(ConfigurationPassInterface::class);

            $this->configuration = new Configuration($this->map->get(), ...[
                $this->pass1->get(),
                $this->pass2->get(),
                $this->pass3->get(),
            ]);

        });

        it('should implement ConfigurationInterface', function () {

            expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

        });

        describe('->map()', function () {

            it('should return the factory map', function () {

                $test = $this->configuration->map();

                expect($test)->toBe($this->map->get());

            });

        });

        describe('->passes()', function () {

            it('should return the configuration passes', function () {

                $test = $this->configuration->passes();

                expect($test[0])->toBe($this->pass1->get());
                expect($test[1])->toBe($this->pass2->get());
                expect($test[2])->toBe($this->pass3->get());

            });

        });

    });

});
