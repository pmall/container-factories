<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\PhpFileConfiguration;
use Quanta\Container\ConfigurationInterface;
use Quanta\Container\ConfigurationEntryInterface;

use Quanta\Container\Factories\Tag;
use Quanta\Container\Factories\Alias;
use Quanta\Container\Factories\Parameter;
use Quanta\Container\Factories\Extension;

use Quanta\Container\Values\Value;
use Quanta\Container\Values\ValueFactoryInterface;

require_once __DIR__ . '/.test/classes.php';

describe('PhpFileConfiguration', function () {

    beforeEach(function () {

        $this->factory = mock(ValueFactoryInterface::class);

    });

    context('when all the files are valid', function () {

        beforeEach(function () {

            $this->configuration = new PhpFileConfiguration($this->factory->get(), ...[
                __DIR__ . '/.test/config/valid/*.php',
            ]);

        });

        it('should implement ConfigurationInterface', function () {

            expect($this->configuration)->toBeAnInstanceOf(ConfigurationInterface::class);

        });

        describe('->entries()', function () {

            it('should return one service provider per php file', function () {

                $this->factory->__invoke->does(function (string $value) {
                    return new Value($value);
                });

                $test = $this->configuration->entries();

                expect($test)->toBeAn('array');
                expect($test)->toHaveLength(7);

                // 0 is valid_full1.php
                expect($test[0])->toBeAnInstanceOf(ConfigurationEntryInterface::class);
                expect($test[0]->factories()->factories())->toEqual([
                    'id1' => new Parameter(new Value('parameter11')),
                    'alias1' => new Alias('id11'),
                    'alias2' => new Alias('id12'),
                    'id2' => new Test\TestFactory('factory11'),
                    'id3' => new Test\TestFactory('factory12'),
                ]);
                expect($test[0]->extensions()->factories())->toEqual([
                    'id3' => new Test\TestFactory('extension11'),
                    'id4' => new Test\TestFactory('extension12'),
                    'tag1' => new Tag('id111', 'id112'),
                    'tag2' => new Tag('id121', 'id122'),
                ]);
                expect($test[0]->tags())->toEqual([
                    'alias1' => ['id11' => []],
                    'alias2' => ['id12' => []],
                    'tag1' => ['id111' => [], 'id112' => []],
                    'tag2' => ['id121' => [], 'id122' => []],
                ]);

                // 1 is valid_full2.php
                expect($test[1])->toBeAnInstanceOf(ConfigurationEntryInterface::class);
                expect($test[1]->factories()->factories())->toEqual([
                    'id1' => new Parameter(new Value('parameter21')),
                    'alias1' => new Alias('id21'),
                    'alias2' => new Alias('id22'),
                    'id2' => new Test\TestFactory('factory21'),
                    'id3' => new Test\TestFactory('factory22'),
                ]);
                expect($test[1]->extensions()->factories())->toEqual([
                    'id3' => new Test\TestFactory('extension21'),
                    'id4' => new Test\TestFactory('extension22'),
                    'tag1' => new Tag('id211', 'id212'),
                    'tag2' => new Tag('id221', 'id222'),
                ]);
                expect($test[1]->tags())->toEqual([
                    'alias1' => ['id21' => []],
                    'alias2' => ['id22' => []],
                    'tag1' => ['id211' => [], 'id212' => []],
                    'tag2' => ['id221' => [], 'id222' => []],
                ]);

                // 2 is valid_only_aliases.php
                expect($test[2])->toBeAnInstanceOf(ConfigurationEntryInterface::class);
                expect($test[2]->factories()->factories())->toEqual([
                    'alias1' => new Alias('id31'),
                    'alias2' => new Alias('id32'),
                ]);
                expect($test[2]->extensions()->factories())->toEqual([]);
                expect($test[2]->tags())->toEqual([
                    'alias1' => ['id31' => []],
                    'alias2' => ['id32' => []],
                ]);

                // 3 is valid_only_extensions.php
                expect($test[3])->toBeAnInstanceOf(ConfigurationEntryInterface::class);
                expect($test[3]->factories()->factories())->toEqual([]);
                expect($test[3]->extensions()->factories())->toEqual([
                    'id1' => new Test\TestFactory('extension31'),
                    'id2' => new Test\TestFactory('extension32'),
                ]);
                expect($test[3]->tags())->toEqual([]);

                // 4 is valid_only_factories.php
                expect($test[4])->toBeAnInstanceOf(ConfigurationEntryInterface::class);
                expect($test[4]->factories()->factories())->toEqual([
                    'id1' => new Test\TestFactory('factory31'),
                    'id2' => new Test\TestFactory('factory32'),
                ]);
                expect($test[4]->extensions()->factories())->toEqual([]);
                expect($test[4]->tags())->toEqual([]);

                // 5 is valid_only_parameters.php
                expect($test[5])->toBeAnInstanceOf(ConfigurationEntryInterface::class);
                expect($test[5]->factories()->factories())->toEqual([
                    'id1' => new Parameter(new Value('parameter31')),
                    'id2' => new Parameter(new Value('parameter32')),
                ]);
                expect($test[5]->extensions()->factories())->toEqual([]);
                expect($test[5]->tags())->toEqual([]);

                // 6 is valid_only_tags.php
                expect($test[6])->toBeAnInstanceOf(ConfigurationEntryInterface::class);
                expect($test[6]->factories()->factories())->toEqual([]);
                expect($test[6]->extensions()->factories())->toEqual([
                    'tag1' => new Tag('id311', 'id312'),
                    'tag2' => new Tag('id321', 'id322'),
                ]);
                expect($test[6]->tags())->toEqual([
                    'tag1' => ['id311' => [], 'id312' => []],
                    'tag2' => ['id321' => [], 'id322' => []],
                ]);

            });

        });

    });

    context('when a file is not valid', function () {

        beforeEach(function () {

            $this->test = function (string $file, string $exception, string ...$strs) {

                $configuration = new PhpFileConfiguration($this->factory->get(), ...[
                    __DIR__ . '/.test/config/valid/full1.php',
                    $file,
                    __DIR__ . '/.test/config/valid/full2.php',
                ]);

                try { $configuration->entries(); }

                catch (Throwable $e) {
                    $test = $e;
                }

                expect(get_class($e))->toEqual($exception);
                expect($e->getMessage())->toContain($file);

                foreach ($strs as $str) {
                    expect($e->getMessage())->toContain($str);
                }
            };

        });

        context('when a value returned by a file is not an array', function () {

            describe('->entries()', function () {

                it('should throw an UnexpectedValueException containing the file path', function () {

                    $file = __DIR__ . '/.test/config/invalid/not_array.php';

                    $this->test($file, UnexpectedValueException::class);

                });

            });

        });

        context('when all the values returned by the files are arrays', function () {

            context('when the parameters key of an array returned by a file is not an array', function () {

                describe('->entries()', function () {

                    it('should throw an UnexpectedValueException containing parameters', function () {

                        $file = __DIR__ . '/.test/config/invalid/not_array/parameters.php';

                        $this->test($file, UnexpectedValueException::class, 'parameters');

                    });

                });

            });

            context('when the aliases key of an array returned by a file is not an array', function () {

                describe('->entries()', function () {

                    it('should throw an UnexpectedValueException containing aliases', function () {

                        $file = __DIR__ . '/.test/config/invalid/not_array/aliases.php';

                        $this->test($file, UnexpectedValueException::class, 'aliases');

                    });

                });

            });

            context('when the \'aliases\' key of an array returned by a file does not contain only strings', function () {

                describe('->entries()', function () {

                    it('should throw an UnexpectedValueException containing aliases', function () {

                        $file = __DIR__ . '/.test/config/invalid/aliases.php';

                        $this->test($file, UnexpectedValueException::class, 'aliases');

                    });

                });

            });

            context('when the \'aliases\' key of an array is sharing an identifier with another array', function () {

                describe('->entries()', function () {

                    it('should throw an LogicException containing the id of the alias and the name of the other array', function () {

                        $file = __DIR__ . '/.test/config/invalid/isect/aliases/parameters.php';

                        $this->test($file, LogicException::class, 'alias', 'id', 'parameters');

                        $file = __DIR__ . '/.test/config/invalid/isect/aliases/factories.php';

                        $this->test($file, LogicException::class, 'alias', 'id', 'factories');

                        $file = __DIR__ . '/.test/config/invalid/isect/aliases/extensions.php';

                        $this->test($file, LogicException::class, 'alias', 'id', 'extensions');

                        $file = __DIR__ . '/.test/config/invalid/isect/aliases/tags.php';

                        $this->test($file, LogicException::class, 'alias', 'id', 'tags');

                    });

                });

            });

            context('when the factories key of an array returned by a file is not an array', function () {

                describe('->entries()', function () {

                    it('should throw an UnexpectedValueException containing factories', function () {

                        $file = __DIR__ . '/.test/config/invalid/not_array/factories.php';

                        $this->test($file, UnexpectedValueException::class, 'factories');

                    });

                });

            });

            context('when the \'factories\' key of an array returned by a file does not contain only callables', function () {

                describe('->entries()', function () {

                    it('should throw an UnexpectedValueException containing factories', function () {

                        $file = __DIR__ . '/.test/config/invalid/factories.php';

                        $this->test($file, UnexpectedValueException::class, 'factories');

                    });

                });

            });

            context('when the extensions key of an array returned by a file is not an array', function () {

                describe('->entries()', function () {

                    it('should throw an UnexpectedValueException containing extensions', function () {

                        $file = __DIR__ . '/.test/config/invalid/not_array/extensions.php';

                        $this->test($file, UnexpectedValueException::class, 'extensions');

                    });

                });

            });

            context('when the \'extensions\' key of an array returned by a file does not contain only callables', function () {

                describe('->entries()', function () {

                    it('should throw an UnexpectedValueException containing extensions', function () {

                        $file = __DIR__ . '/.test/config/invalid/extensions.php';

                        $this->test($file, UnexpectedValueException::class, 'extensions');

                    });

                });

            });

            context('when the tags key of an array returned by a file is not an array', function () {

                describe('->entries()', function () {

                    it('should throw an UnexpectedValueException containing tags', function () {

                        $file = __DIR__ . '/.test/config/invalid/not_array/tags.php';

                        $this->test($file, UnexpectedValueException::class, 'tags');

                    });

                });

            });

            context('when the \'tags\' key of an array returned by a file does not contain only arrays', function () {

                describe('->entries()', function () {

                    it('should throw an UnexpectedValueException containing tags', function () {

                        $file = __DIR__ . '/.test/config/invalid/tags.php';

                        $this->test($file, UnexpectedValueException::class, 'tags');

                    });

                });

            });

            context('when a tag attribute is not an array', function () {

                describe('->entries()', function () {

                    it('should throw an UnexpectedValueException containing the tag name', function () {

                        $file = __DIR__ . '/.test/config/invalid/tags_attributes.php';

                        $this->test($file, UnexpectedValueException::class, 'id22');

                    });

                });

            });

            context('when the \'tags\' key of an array is sharing an identifier with another array', function () {

                describe('->entries()', function () {

                    it('should throw an LogicException containing the id of the tag and the name of the other array', function () {

                        $file = __DIR__ . '/.test/config/invalid/isect/tags/parameters.php';

                        $this->test($file, LogicException::class, 'tag', 'id', 'parameters');

                        $file = __DIR__ . '/.test/config/invalid/isect/tags/aliases.php';

                        $this->test($file, LogicException::class, 'alias', 'id', 'tags');

                        $file = __DIR__ . '/.test/config/invalid/isect/tags/factories.php';

                        $this->test($file, LogicException::class, 'tag', 'id', 'factories');

                        $file = __DIR__ . '/.test/config/invalid/isect/tags/extensions.php';

                        $this->test($file, LogicException::class, 'tag', 'id', 'extensions');

                    });

                });

            });

        });

    });

});
