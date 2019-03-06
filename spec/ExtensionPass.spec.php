<?php

use Quanta\Container\ExtensionPass;
use Quanta\Container\ProcessingPassInterface;
use Quanta\Container\Factories\Extension;

describe('ExtensionPass::instance()', function () {

    it('should return a new ExtensionPass with the given id and extension', function () {

        $extension = function () {};

        $test = ExtensionPass::instance('id', $extension);

        expect($test)->toEqual(new ExtensionPass('id', $extension));

    });

});

describe('ExtensionPass', function () {

    beforeEach(function () {

        $this->pass = new ExtensionPass('id', $this->extension = function () {});

    });

    it('should implement ProcessingPassInterface', function () {

        expect($this->pass)->toBeAnInstanceOf(ProcessingPassInterface::class);

    });

    context('->processed()', function () {

        context('when the id is not present in the given associative array of factories', function () {

            it('should return the given associative array of factories', function () {

                $test = $this->pass->processed([
                    'id1' => $factory1 = function () {},
                    'id2' => $factory2 = function () {},
                    'id3' => $factory3 = function () {},
                ]);

                expect($test)->toBeAn('array');
                expect($test)->toHaveLength(3);
                expect($test['id1'])->toBe($factory1);
                expect($test['id2'])->toBe($factory2);
                expect($test['id3'])->toBe($factory3);

            });

        });

        context('when the id is present in the given associative array of factories', function () {

            it('should extend the factory associated to the id', function () {

                $test = $this->pass->processed([
                    'id1' => $factory1 = function () {},
                    'id' => $factory2 = function () {},
                    'id3' => $factory3 = function () {},
                ]);

                expect($test)->toBeAn('array');
                expect($test)->toHaveLength(3);
                expect($test['id1'])->toBe($factory1);
                expect($test['id'])->toEqual(new Extension($factory2, $this->extension));
                expect($test['id3'])->toBe($factory3);

            });

        });

    });

});
