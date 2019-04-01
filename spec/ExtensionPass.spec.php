<?php

use Quanta\Container\Extension;
use Quanta\Container\ExtensionPass;
use Quanta\Container\ProcessingPassInterface;

describe('ExtensionPass::instance()', function () {

    it('should return a new ExtensionPass with the given id and extension', function () {

        $extension = function () {};

        $test = ExtensionPass::instance('id', $extension);

        expect($test)->toEqual(new ExtensionPass('id', $extension));

    });

});

describe('ExtensionPass', function () {

    beforeEach(function () {

        $this->extension = function () {};

        $this->pass = new ExtensionPass('id', $this->extension);

    });

    it('should implement ProcessingPassInterface', function () {

        expect($this->pass)->toBeAnInstanceOf(ProcessingPassInterface::class);

    });

    context('->aliases()', function () {

        it('should return an empty array', function () {

            $test = $this->pass->aliases('id');

            expect($test)->toEqual([]);

        });

    });

    context('->tags()', function () {

        it('should return an empty array', function () {

            $test = $this->pass->tags('id1', 'id2', 'id3');

            expect($test)->toEqual([]);

        });

    });

    context('->processed()', function () {

        beforeEach(function () {

            $this->factory = function () {};

        });

        context('when given id is not the id of the extended factory', function () {

            it('should return the given factory', function () {

                $test = $this->pass->processed('test', $this->factory);

                expect($test)->toBe($this->factory);

            });

        });

        context('when the given id is the id of the extended factory', function () {

            it('should extend the given factory with the extension', function () {

                $test = $this->pass->processed('id', $this->factory);

                expect($test)->toEqual(new Extension($this->factory, $this->extension));

            });

        });

    });

});
