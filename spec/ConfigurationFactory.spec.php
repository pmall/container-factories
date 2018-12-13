<?php

use Quanta\Utils\VendorDirectory;
use Quanta\Container\ConfigurationFactory;
use Quanta\Container\PhpFileConfiguration;
use Quanta\Container\ServiceProviderCollection;

describe('ConfigurationFactory', function () {

    beforeEach(function () {

        $this->factory = new ConfigurationFactory;

    });

    describe('->files()', function () {

        it('should return a PhpFileConfiguration using the given glob patterns', function () {

            $patterns = ['pattern1', 'pattern2', 'pattern3'];

            $test = $this->factory->files(...$patterns);

            expect($test)->toEqual(new PhpFileConfiguration(...$patterns));

        });

    });

    describe('->vendor()', function () {

        beforeEach(function () {

            $this->collection = new VendorDirectory('path');

        });

        context('when no extra arguments are given', function () {

            it('should return a ServiceProviderCollection using a VendorDirectory using the given path', function () {

                $test = $this->factory->vendor('path');

                $expected = new ServiceProviderCollection($this->collection);

                expect($test)->toEqual($expected);

            });

        });

        context('when extra arguments are given', function () {

            it('should return a ServiceProviderCollection using the given extra arguments', function () {

                $test = $this->factory->vendor('path', 'pattern', 'bl1', 'bl2');

                $expected = new ServiceProviderCollection($this->collection, 'pattern', 'bl1', 'bl2');

                expect($test)->toEqual($expected);

            });

        });

    });

});
