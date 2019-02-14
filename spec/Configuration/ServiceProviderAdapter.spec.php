<?php

use function Eloquent\Phony\Kahlan\mock;

use Interop\Container\ServiceProviderInterface;

use Quanta\Container\FactoryMap;
use Quanta\Container\Configuration\ServiceProviderAdapter;
use Quanta\Container\Configuration\ConfigurationEntryInterface;

use Quanta\Exceptions\ReturnTypeErrorMessage;
use Quanta\Exceptions\ArrayReturnTypeErrorMessage;

require_once __DIR__ . '/../.test/classes.php';

describe('ServiceProviderAdapter', function () {

    beforeEach(function () {

        $this->delegate = mock(ServiceProviderInterface::class);

        $this->configuration = new ServiceProviderAdapter($this->delegate->get());

    });

    it('should implement ConfigurationEntryInterface', function () {

        expect($this->configuration)->toBeAnInstanceOf(ConfigurationEntryInterface::class);

    });

    describe('->factories()', function () {

        context('when the service provider ->getFactories() method does not return an array', function () {

            it('should throw an UnexpectedValueException', function () {

                $this->delegate->getFactories->returns(1);

                expect([$this->configuration, 'factories'])->toThrow(new UnexpectedValueException(
                    (string) new ReturnTypeErrorMessage(
                        sprintf('%s::getFactories()', get_class($this->delegate->get())), 'array', 1
                    )
                ));

            });

        });

        context('when the service provider ->getFactories() method returns an array', function () {

            context('when all the values of the array returned by the service provider ->getFactories() method are callable', function () {

                it('should return a factory map from the factories returned by the service provider ->getFactories() method', function () {

                    $factories = [
                        'id1' => new Test\TestFactory('factory1'),
                        'id2' => new Test\TestFactory('factory2'),
                        'id3' => new Test\TestFactory('factory3'),
                    ];

                    $this->delegate->getFactories->returns($factories);

                    $test = $this->configuration->factories();

                    expect($test)->toEqual(new FactoryMap($factories));

                });

            });

            context('when a value of the array returned by the service provider ->getFactories() method is not a callable', function () {

                it('should throw an UnexpectedValueException', function () {

                    $factories = [
                        'id1' => function () {},
                        'id2' => 1,
                        'id3' => function () {},
                    ];

                    $this->delegate->getFactories->returns($factories);

                    expect([$this->configuration, 'factories'])->toThrow(new UnexpectedValueException(
                        (string) new ArrayReturnTypeErrorMessage(
                            sprintf('%s::getFactories()', get_class($this->delegate->get())), 'callable', $factories
                        )
                    ));

                });

            });

        });

    });

    describe('->extensions()', function () {

        context('when the service provider ->getExtensions() method does not return an array', function () {

            it('should throw an UnexpectedValueException', function () {

                $this->delegate->getExtensions->returns(1);

                expect([$this->configuration, 'extensions'])->toThrow(new UnexpectedValueException(
                    (string) new ReturnTypeErrorMessage(
                        sprintf('%s::getExtensions()', get_class($this->delegate->get())), 'array', 1
                    )
                ));

            });

        });

        context('when the service provider ->getExtensions() method returns an array', function () {

            context('when all the values of the array returned by the service provider ->getExtensions() method are callable', function () {

                it('should return a factory map from the extensions returned by the service provider ->getExtensions() method', function () {

                    $extensions = [
                        'id1' => new Test\TestFactory('factory1'),
                        'id2' => new Test\TestFactory('factory2'),
                        'id3' => new Test\TestFactory('factory3'),
                    ];

                    $this->delegate->getExtensions->returns($extensions);

                    $test = $this->configuration->extensions();

                    expect($test)->toEqual(new FactoryMap($extensions));

                });

            });

            context('when a value of the array returned by the service provider ->getExtensions() method is not a callable', function () {

                it('should throw an UnexpectedValueException', function () {

                    $extensions = [
                        'id1' => function () {},
                        'id2' => 1,
                        'id3' => function () {},
                    ];

                    $this->delegate->getExtensions->returns($extensions);

                    expect([$this->configuration, 'extensions'])->toThrow(new UnexpectedValueException(
                        (string) new ArrayReturnTypeErrorMessage(
                            sprintf('%s::getExtensions()', get_class($this->delegate->get())), 'callable', $extensions
                        )
                    ));

                });

            });

        });

    });

    describe('->metadata()', function () {

        it('should return an empty array', function () {

            $test = $this->configuration->metadata();

            expect($test)->toEqual([]);

        });

    });

    describe('->passes()', function () {

        it('should return an empty array', function () {

            $test = $this->configuration->passes();

            expect($test)->toEqual([]);

        });

    });

});
