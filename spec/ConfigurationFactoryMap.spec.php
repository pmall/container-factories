<?php

use function Eloquent\Phony\Kahlan\mock;

use Test\TestFactory;

use Interop\Container\ServiceProviderInterface;

use Quanta\Container\FactoryMapInterface;
use Quanta\Container\ConfigurationInterface;
use Quanta\Container\ConfigurationFactoryMap;

use Quanta\Exceptions\ReturnTypeErrorMessage;
use Quanta\Exceptions\ArrayReturnTypeErrorMessage;

use Quanta\Container\Factories\Extension;

require_once __DIR__ . '/.test/classes.php';

describe('ConfigurationFactoryMap', function () {

    it('should implement FactoryMapInterface', function () {

        expect(new ConfigurationFactoryMap)->toBeAnInstanceOf(FactoryMapInterface::class);

    });

    describe('->factories()', function () {

        context('when there is no configuration', function () {

            it('should return an empty array', function () {

                $map = new ConfigurationFactoryMap;

                $test = $map->factories();

                expect($test)->toEqual([]);

            });

        });

        context('when there is at least one configuration', function () {

            beforeEach(function () {

                $this->configuration1 = mock(ConfigurationInterface::class);
                $this->configuration2 = mock(ConfigurationInterface::class);

                $this->map = new ConfigurationFactoryMap(...[
                    $this->configuration1->get(),
                    $this->configuration2->get(),
                ]);

            });

            context('when no service provider is provided by the configurations', function () {

                it('should return an empty array', function () {

                    $this->configuration1->providers->returns([]);
                    $this->configuration2->providers->returns([]);

                    $test = $this->map->factories();

                    expect($test)->toEqual([]);

                });

            });

            context('when at least one service provider is provided by the configurations', function () {

                beforeEach(function () {

                    $this->provider1 = mock(ServiceProviderInterface::class);
                    $this->provider2 = mock(ServiceProviderInterface::class);
                    $this->provider3 = mock(ServiceProviderInterface::class);

                    $this->provider1->getFactories->returns([]);
                    $this->provider2->getFactories->returns([]);
                    $this->provider3->getFactories->returns([]);
                    $this->provider1->getExtensions->returns([]);
                    $this->provider2->getExtensions->returns([]);
                    $this->provider3->getExtensions->returns([]);

                    $this->configuration1->providers->returns([
                        $this->provider1->get(),
                        $this->provider2->get(),
                    ]);

                    $this->configuration2->providers->returns([
                        $this->provider3->get(),
                    ]);

                });

                context('when all service providers return array of callable values', function () {

                    beforeEach(function () {

                        $this->provider1->getFactories->returns([
                            'id1' => new TestFactory('f11'),
                            'id2' => new TestFactory('f12'),
                            'id3' => new TestFactory('f13'),
                        ]);

                        $this->provider2->getFactories->returns([
                            'id2' => new TestFactory('f22'),
                            'id3' => new TestFactory('f23'),
                            'id4' => new TestFactory('f24'),
                        ]);

                        $this->provider3->getFactories->returns([
                            'id3' => new TestFactory('f33'),
                            'id4' => new TestFactory('f34'),
                            'id5' => new TestFactory('f35'),
                        ]);

                    });

                    it('should merge the factories provided by the service providers', function () {

                        $test = $this->map->factories();

                        expect($test)->toBeAn('array');
                        expect($test)->toHaveLength(5);
                        expect($test['id1'])->toEqual(new TestFactory('f11'));
                        expect($test['id2'])->toEqual(new TestFactory('f22'));
                        expect($test['id3'])->toEqual(new TestFactory('f33'));
                        expect($test['id4'])->toEqual(new TestFactory('f34'));
                        expect($test['id5'])->toEqual(new TestFactory('f35'));

                    });

                    it('should extend the factories with the extensions provided by the service providers', function () {

                        $this->provider1->getExtensions->returns([
                            'id2' => new TestFactory('e12'),
                            'id3' => new TestFactory('e13'),
                        ]);

                        $this->provider2->getExtensions->returns([
                            'id1' => new TestFactory('e21'),
                            'id3' => new TestFactory('e23'),
                        ]);

                        $this->provider3->getExtensions->returns([
                            'id1' => new TestFactory('e31'),
                            'id2' => new TestFactory('e32'),
                        ]);

                        $test = $this->map->factories();

                        expect($test)->toBeAn('array');
                        expect($test)->toHaveLength(5);
                        expect($test['id1'])->toEqual(new Extension(
                            new Extension(new TestFactory('f11'), new TestFactory('e21')), new TestFactory('e31')
                        ));
                        expect($test['id2'])->toEqual(new Extension(
                            new Extension(new TestFactory('f22'), new TestFactory('e12')), new TestFactory('e32')
                        ));
                        expect($test['id3'])->toEqual(new Extension(
                            new Extension(new TestFactory('f33'), new TestFactory('e13')), new TestFactory('e23')
                        ));

                    });

                    it('should return extensions with no corresponding factory', function () {

                        $this->provider1->getExtensions->returns([
                            'id6' => new TestFactory('e16'),
                        ]);

                        $this->provider2->getExtensions->returns([
                            'id6' => new TestFactory('e26'),
                        ]);

                        $this->provider3->getExtensions->returns([
                            'id6' => new TestFactory('e36'),
                        ]);

                        $test = $this->map->factories();

                        expect($test)->toBeAn('array');
                        expect($test)->toHaveLength(6);
                        expect($test['id6'])->toEqual(new Extension(
                            new Extension(new TestFactory('e16'), new TestFactory('e26')), new TestFactory('e36')
                        ));

                    });

                    context('when at least one service provider ->getFactories() method does not return an array', function () {

                        it('should throw an UnexpectedValueException', function () {

                            $this->provider2->getFactories->returns(1);

                            expect([$this->map, 'factories'])->toThrow(
                                new UnexpectedValueException(
                                    (string) new ReturnTypeErrorMessage(
                                        sprintf('%s::getFactories()', get_class($this->provider2->get())),
                                        'array',
                                        1
                                    )
                                )
                            );

                        });

                    });

                    context('when all service providers ->getFactories() methods return array', function () {

                        context('when at least one array contains a non callable value', function () {

                            it('should throw an UnexpectedValueException', function () {

                                $invalid = [
                                    'id2' => function () {},
                                    'id3' => 1,
                                    'id4' => function () {},
                                ];

                                $this->provider2->getFactories->returns($invalid);

                                expect([$this->map, 'factories'])->toThrow(
                                    new UnexpectedValueException(
                                        (string) new ArrayReturnTypeErrorMessage(
                                            sprintf('%s::getFactories()', get_class($this->provider2->get())),
                                            'callable',
                                            $invalid
                                        )
                                    )
                                );

                            });

                        });

                    });

                    context('when at least one service provider ->getExtensions() method does not return an array', function () {

                        it('should throw an UnexpectedValueException', function () {

                            $this->provider2->getExtensions->returns(1);

                            expect([$this->map, 'factories'])->toThrow(
                                new UnexpectedValueException(
                                    (string) new ReturnTypeErrorMessage(
                                        sprintf('%s::getExtensions()', get_class($this->provider2->get())),
                                        'array',
                                        1
                                    )
                                )
                            );

                        });

                    });

                    context('when all service providers ->getExtensions() methods return array', function () {

                        context('when at least one array contains a non callable value', function () {

                            it('should throw an UnexpectedValueException', function () {

                                $invalid = [
                                    'id2' => function () {},
                                    'id3' => 1,
                                    'id4' => function () {},
                                ];

                                $this->provider2->getExtensions->returns($invalid);

                                expect([$this->map, 'factories'])->toThrow(
                                    new UnexpectedValueException(
                                        (string) new ArrayReturnTypeErrorMessage(
                                            sprintf('%s::getExtensions()', get_class($this->provider2->get())),
                                            'callable',
                                            $invalid
                                        )
                                    )
                                );

                            });

                        });

                    });

                });

            });

        });

    });

});
