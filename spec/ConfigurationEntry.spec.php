<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\ConfigurationEntry;
use Quanta\Container\ConfigurationEntryInterface;
use Quanta\Container\Maps\FactoryMapInterface;
use Quanta\Container\Passes\ConfigurationPassInterface;

describe('ConfigurationEntry', function () {

    beforeEach(function () {

        $this->factories = mock(FactoryMapInterface::class);
        $this->extensions = mock(FactoryMapInterface::class);

        $this->pass1 = mock(ConfigurationPassInterface::class);
        $this->pass2 = mock(ConfigurationPassInterface::class);
        $this->pass3 = mock(ConfigurationPassInterface::class);

        $this->entry = new ConfigurationEntry(...[
            $this->factories->get(),
            $this->extensions->get(),
            ['id1' => ['k1' => 'm1']],
            $this->pass1->get(),
            $this->pass2->get(),
            $this->pass3->get(),
        ]);

    });

    it('should implement ConfigurationEntryInterface', function () {

        expect($this->entry)->toBeAnInstanceOf(ConfigurationEntryInterface::class);

    });

    describe('->factories()', function () {

        it('should return the factory map', function () {

            $test = $this->entry->factories();

            expect($test)->toBe($this->factories->get());

        });

    });

    describe('->extensions()', function () {

        it('should return the extension map', function () {

            $test = $this->entry->extensions();

            expect($test)->toBe($this->extensions->get());

        });

    });

    describe('->metadata()', function () {

        it('should return the metadata', function () {

            $test = $this->entry->metadata();

            expect($test)->toEqual(['id1' => ['k1' => 'm1']]);

        });

    });

    describe('->passes()', function () {

        it('should return the configuration passes', function () {

            $test = $this->entry->passes();

            expect($test)->toBeAn('array');
            expect($test)->toHaveLength(3);
            expect($test[0])->toBe($this->pass1->get());
            expect($test[1])->toBe($this->pass2->get());
            expect($test[2])->toBe($this->pass3->get());

        });

    });

});
