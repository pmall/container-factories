<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\MergedProcessingPass;
use Quanta\Container\ProcessingPassInterface;
use Quanta\Container\Configuration\ConfigurationUnit;
use Quanta\Container\Configuration\ConfigurationUnitInterface;

describe('ConfigurationUnit', function () {

    context('when the associative array of factories is empty', function () {

        context('when there is no processing pass', function () {

            beforeEach(function () {

                $this->unit = new ConfigurationUnit([]);

            });

            it('should implement ConfigurationUnitInterface', function () {

                expect($this->unit)->toBeAnInstanceOf(ConfigurationUnitInterface::class);

            });

            describe('->factories()', function () {

                it('should return an empty array', function () {

                    $test = $this->unit->factories();

                    expect($test)->toEqual([]);

                });

            });

            describe('->pass()', function () {

                it('should return an empty merged processing pass', function () {

                    $test = $this->unit->pass();

                    expect($test)->toEqual(new MergedProcessingPass);

                });

            });

        });

        context('when there is at least one processing pass', function () {

            beforeEach(function () {

                $this->pass1 = mock(ProcessingPassInterface::class);
                $this->pass2 = mock(ProcessingPassInterface::class);
                $this->pass3 = mock(ProcessingPassInterface::class);

                $this->unit = new ConfigurationUnit([], ...[
                    $this->pass1->get(),
                    $this->pass2->get(),
                    $this->pass3->get(),
                ]);

            });

            it('should implement ConfigurationUnitInterface', function () {

                expect($this->unit)->toBeAnInstanceOf(ConfigurationUnitInterface::class);

            });

            describe('->factories()', function () {

                it('should return an empty array', function () {

                    $test = $this->unit->factories();

                    expect($test)->toEqual([]);

                });

            });

            describe('->pass()', function () {

                it('should return a merged processing pass', function () {

                    $test = $this->unit->pass();

                    expect($test)->toEqual(new MergedProcessingPass(
                        $this->pass1->get(),
                        $this->pass2->get(),
                        $this->pass3->get()
                    ));

                });

            });

        });

    });

    context('when the associative array of factories is not empty', function () {

        beforeEach(function () {

            $this->factory1 = function () {};
            $this->factory2 = function () {};
            $this->factory3 = function () {};

        });

        context('when all the values of the associative array of factories are callables', function () {

            context('when there is no processing pass', function () {

                beforeEach(function () {

                    $this->unit = new ConfigurationUnit([
                        'id1' => $this->factory1,
                        'id2' => $this->factory2,
                        'id3' => $this->factory3,
                    ]);

                });

                it('should implement ConfigurationUnitInterface', function () {

                    expect($this->unit)->toBeAnInstanceOf(ConfigurationUnitInterface::class);

                });

                describe('->factories()', function () {

                    it('should return an empty array', function () {

                        $test = $this->unit->factories();

                        expect($test)->toEqual([
                            'id1' => $this->factory1,
                            'id2' => $this->factory2,
                            'id3' => $this->factory3,
                        ]);

                    });

                });

                describe('->pass()', function () {

                    it('should return an empty merged processing pass', function () {

                        $test = $this->unit->pass();

                        expect($test)->toEqual(new MergedProcessingPass);

                    });

                });

            });

            context('when there is at least one processing pass', function () {

                beforeEach(function () {

                    $this->pass1 = mock(ProcessingPassInterface::class);
                    $this->pass2 = mock(ProcessingPassInterface::class);
                    $this->pass3 = mock(ProcessingPassInterface::class);

                    $this->unit = new ConfigurationUnit([
                        'id1' => $this->factory1,
                        'id2' => $this->factory2,
                        'id3' => $this->factory3,
                    ], ...[
                        $this->pass1->get(),
                        $this->pass2->get(),
                        $this->pass3->get(),
                    ]);

                });

                it('should implement ConfigurationUnitInterface', function () {

                    expect($this->unit)->toBeAnInstanceOf(ConfigurationUnitInterface::class);

                });

                describe('->factories()', function () {

                    it('should return an empty array', function () {

                        $test = $this->unit->factories();

                        expect($test)->toEqual([
                            'id1' => $this->factory1,
                            'id2' => $this->factory2,
                            'id3' => $this->factory3,
                        ]);

                    });

                });

                describe('->pass()', function () {

                    it('should return a merged processing pass', function () {

                        $test = $this->unit->pass();

                        expect($test)->toEqual(new MergedProcessingPass(
                            $this->pass1->get(),
                            $this->pass2->get(),
                            $this->pass3->get()
                        ));

                    });

                });

            });

        });

        context('when a value of the associative array of factories is not a callable', function () {

            it('should throw an InvalidArgumentException', function () {

                $test = function () {
                    new ConfigurationUnit([
                        'id1' => $this->factory1,
                        'id2' => 1,
                        'id3' => $this->factory3,
                    ]);
                };

                expect($test)->toThrow(new InvalidArgumentException);

            });

        });

    });

});
