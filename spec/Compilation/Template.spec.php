<?php

use function Eloquent\Phony\Kahlan\mock;

use Quanta\Container\Compilation\Template;
use Quanta\Container\Compilation\CallableCompiler;
use Quanta\Container\Compilation\ClosureCompilerInterface;

describe('Template::withDummyClosureCompiler()', function () {

    context('when no container variable name is given', function () {

        it('should return a template with a dummy closure compiler', function () {

            $test = Template::withDummyClosureCompiler();

            expect($test)->toEqual(new Template(
                new CallableCompiler(
                    new Quanta\Container\Compilation\DummyClosureCompiler
                )
            ));

        });

    });

    context('when a container variable name is given', function () {

        it('should return a template with a dummy closure compiler and the container variable name', function () {

            $test = Template::withDummyClosureCompiler('container_var_name');

            expect($test)->toEqual(new Template(
                new CallableCompiler(
                    new Quanta\Container\Compilation\DummyClosureCompiler
                ),
                'container_var_name'
            ));

        });

    });

});

describe('Template::withClosureCompiler()', function () {

    context('when no container variable name is given', function () {

        it('should return a template with the given closure compiler', function () {

            $compiler = mock(ClosureCompilerInterface::class);

            $test = Template::withClosureCompiler($compiler->get());

            expect($test)->toEqual(new Template(
                new CallableCompiler(
                    $compiler->get()
                )
            ));

        });

    });

    context('when a container variable name is given', function () {

        it('should return a template with the given closure compiler and the container variable name', function () {

            $compiler = mock(ClosureCompilerInterface::class);

            $test = Template::withClosureCompiler($compiler->get(), 'container_var_name');

            expect($test)->toEqual(new Template(
                new CallableCompiler(
                    $compiler->get()
                ),
                'container_var_name'
            ));

        });

    });

});

describe('Template', function () {

    beforeEach(function () {

        $this->delegate = mock(ClosureCompilerInterface::class);
        $this->compiler = new CallableCompiler($this->delegate->get());

    });

    context('when there is no container variable name', function () {

        beforeEach(function () {

            $this->template = new Template($this->compiler);

        });

        describe('->containerVariableName()', function () {

            it('should return \'container\'', function () {

                $test = $this->template->containerVariableName();

                expect($test)->toEqual('container');

            });

        });

        describe('->withPrevious()', function () {

            it('should return a new Template with the given previous parameter', function () {

                $test = $this->template->withPrevious('previous');

                expect($test)->not->toBe($this->template);
                expect($test)->toEqual(new Template($this->compiler, 'container', 'previous'));

            });

        });

        describe('->withBody()', function () {

            it('should return a new Template with the given body parts', function () {

                $test = $this->template->withBody('body1', 'body2', 'body3');

                expect($test)->not->toBe($this->template);
                expect($test)->toEqual(new Template($this->compiler, 'container', '', ...[
                    'body1', 'body2', 'body3',
                ]));

            });

        });

        describe('->withBodyf()', function () {

            it('should return a new Template with a body part created by applying the given arguments to the given sprintf format', function () {

                $test = $this->template->withBodyf('[%s, %s]', 'value1', 'value2');

                expect($test)->not->toBe($this->template);
                expect($test)->toEqual(new Template($this->compiler, 'container', '', '[value1, value2]'));

            });

        });

        describe('->withCallable()', function () {

            it('should return a new Template with a body part assigning the variable with the given name to the given callable', function () {

                $callable = function () {};

                $this->delegate->compiled
                    ->with(Kahlan\Arg::toBe($callable))
                    ->returns('callable');

                $test = $this->template->withCallable('variable', $callable);

                expect($test)->not->toBe($this->template);
                expect($test)->toEqual(new Template($this->compiler, 'container', '', '$variable = callable;'));

            });

        });

        describe('->strWithReturn()', function () {

            it('should return a string representation of a container factory with \'container\' as container variable name returning the given value', function () {

                $test = $this->template->strWithReturn('value');

                expect($test)->toEqual(<<<'EOT'
function (\Psr\Container\ContainerInterface $container) {
    return value;
}
EOT
                );

            });

        });

        describe('->strWithReturnf()', function () {

            it('should return a string representation of a container factory with \'container\' as container variable name returning the value created by applying the given arguments to the given sprintf format', function () {

                $test = $this->template->strWithReturnf('[%s, %s]', 'value1', 'value2');

                expect($test)->toEqual(<<<'EOT'
function (\Psr\Container\ContainerInterface $container) {
    return [value1, value2];
}
EOT
                );

            });

        });

        describe('->__toString()', function () {

            it('should return the string representation of a factory with \'container\' as container variable name and no body part', function () {

                expect((string) $this->template)->toEqual(<<<'EOT'
function (\Psr\Container\ContainerInterface $container) {
    //
}
EOT
                );

            });

        });

    });

    context('when there is a container variable name', function () {

        context('when there is no previous parameter', function () {

            beforeEach(function () {

                $this->template = new Template($this->compiler, 'c');

            });

            describe('->containerVariableName()', function () {

                it('should return the container variable name', function () {

                    $test = $this->template->containerVariableName();

                    expect($test)->toEqual('c');

                });

            });

            describe('->withPrevious()', function () {

                it('should return a new Template with the given previous parameter', function () {

                    $test = $this->template->withPrevious('previous');

                    expect($test)->not->toBe($this->template);
                    expect($test)->toEqual(new Template($this->compiler, 'c', 'previous'));

                });

            });

            describe('->withBody()', function () {

                it('should return a new Template with the given body parts', function () {

                    $test = $this->template->withBody('body1', 'body2', 'body3');

                    expect($test)->not->toBe($this->template);
                    expect($test)->toEqual(new Template($this->compiler, 'c', '', ...[
                        'body1', 'body2', 'body3',
                    ]));

                });

            });

            describe('->withBodyf()', function () {

                it('should return a new Template with a body part created by applying the given arguments to the given sprintf format', function () {

                    $test = $this->template->withBodyf('[%s, %s]', 'value1', 'value2');

                    expect($test)->not->toBe($this->template);
                    expect($test)->toEqual(new Template($this->compiler, 'c', '', '[value1, value2]'));

                });

            });

            describe('->withCallable()', function () {

                it('should return a new Template with a body part assigning the variable with the given name to the given callable', function () {

                    $callable = function () {};

                    $this->delegate->compiled
                        ->with(Kahlan\Arg::toBe($callable))
                        ->returns('callable');

                    $test = $this->template->withCallable('variable', $callable);

                    expect($test)->not->toBe($this->template);
                    expect($test)->toEqual(new Template($this->compiler, 'c', '', '$variable = callable;'));

                });

            });

            describe('->strWithReturn()', function () {

                it('should return a string representation of a container factory with the container variable name returning the given value', function () {

                    $test = $this->template->strWithReturn('value');

                    expect($test)->toEqual(<<<'EOT'
function (\Psr\Container\ContainerInterface $c) {
    return value;
}
EOT
                    );

                });

            });

            describe('->strWithReturnf()', function () {

                it('should return a string representation of a container factory with the container variable name returning the value created by applying the given arguments to the given sprintf format', function () {

                    $test = $this->template->strWithReturnf('[%s, %s]', 'value1', 'value2');

                    expect($test)->toEqual(<<<'EOT'
function (\Psr\Container\ContainerInterface $c) {
    return [value1, value2];
}
EOT
                    );

                });


            });

            describe('->__toString()', function () {

                it('should return the string representation of a factory with the container variable name and no body part', function () {

                    expect((string) $this->template)->toEqual(<<<'EOT'
function (\Psr\Container\ContainerInterface $c) {
    //
}
EOT
                    );

                });

            });

        });

        context('when there is a previous parameter', function () {

            context('when there is no body part', function () {

                beforeEach(function () {

                    $this->template = new Template($this->compiler, 'c', 'p');

                });

                describe('->containerVariableName()', function () {

                    it('should return the container variable name', function () {

                        $test = $this->template->containerVariableName();

                        expect($test)->toEqual('c');

                    });

                });

                describe('->withPrevious()', function () {

                    it('should return a new Template with the given previous parameter', function () {

                        $test = $this->template->withPrevious('previous');

                        expect($test)->not->toBe($this->template);
                        expect($test)->toEqual(new Template($this->compiler, 'c', 'previous'));

                    });

                });

                describe('->withBody()', function () {

                    it('should return a new Template with the given body parts', function () {

                        $test = $this->template->withBody('body1', 'body2', 'body3');

                        expect($test)->not->toBe($this->template);
                        expect($test)->toEqual(new Template($this->compiler, 'c', 'p', ...[
                            'body1', 'body2', 'body3',
                        ]));

                    });

                });

                describe('->withBodyf()', function () {

                    it('should return a new Template with a body part created by applying the given arguments to the given sprintf format', function () {

                        $test = $this->template->withBodyf('[%s, %s]', 'value1', 'value2');

                        expect($test)->not->toBe($this->template);
                        expect($test)->toEqual(new Template($this->compiler, 'c', 'p', '[value1, value2]'));

                    });

                });

                describe('->withCallable()', function () {

                    it('should return a new Template with a body part assigning the variable with the given name to the given callable', function () {

                        $callable = function () {};

                        $this->delegate->compiled
                            ->with(Kahlan\Arg::toBe($callable))
                            ->returns('callable');

                        $test = $this->template->withCallable('variable', $callable);

                        expect($test)->not->toBe($this->template);
                        expect($test)->toEqual(new Template($this->compiler, 'c', 'p', '$variable = callable;'));

                    });

                });

                describe('->strWithReturn()', function () {

                    it('should return a string representation of a container factory with the container variable name, the previous parameter and returning the given value', function () {

                        $test = $this->template->strWithReturn('value');

                        expect($test)->toEqual(<<<'EOT'
function (\Psr\Container\ContainerInterface $c, p) {
    return value;
}
EOT
                        );

                    });

                });

                describe('->strWithReturnf()', function () {

                    it('should return a string representation of a container factory with the container variable name, the previous parameter and returning the value created by applying the given arguments to the given sprintf format', function () {

                        $test = $this->template->strWithReturnf('[%s, %s]', 'value1', 'value2');

                        expect($test)->toEqual(<<<'EOT'
function (\Psr\Container\ContainerInterface $c, p) {
    return [value1, value2];
}
EOT
                        );

                    });


                });

                describe('->__toString()', function () {

                    it('should return the string representation of a factory with the container variable name, the previous parameter and no body part', function () {

                        expect((string) $this->template)->toEqual(<<<'EOT'
function (\Psr\Container\ContainerInterface $c, p) {
    //
}
EOT
                        );

                    });

                });

            });

            context('when there is body parts', function () {

                beforeEach(function () {

                    $this->template = new Template(...[
                        $this->compiler, 'c', 'p', 'body1', 'body2', 'body3'
                    ]);

                });

                describe('->containerVariableName()', function () {

                    it('should return the container variable name', function () {

                        $test = $this->template->containerVariableName();

                        expect($test)->toEqual('c');

                    });

                });

                describe('->withPrevious()', function () {

                    it('should return a new Template with the given previous parameter', function () {

                        $test = $this->template->withPrevious('previous');

                        expect($test)->not->toBe($this->template);
                        expect($test)->toEqual(new Template($this->compiler, 'c', 'previous', ...[
                            'body1', 'body2', 'body3',
                        ]));

                    });

                });

                describe('->withBody()', function () {

                    it('should return a new Template with the given body parts added', function () {

                        $test = $this->template->withBody('body4', 'body5', 'body6');

                        expect($test)->not->toBe($this->template);
                        expect($test)->toEqual(new Template($this->compiler, 'c', 'p', ...[
                            'body1', 'body2', 'body3',
                            'body4', 'body5', 'body6',
                        ]));

                    });

                });

                describe('->withBodyf()', function () {

                    it('should return a new Template with a body part created by applying the given arguments to the given sprintf format added', function () {

                        $test = $this->template->withBodyf('[%s, %s]', 'value1', 'value2');

                        expect($test)->not->toBe($this->template);
                        expect($test)->toEqual(new Template($this->compiler, 'c', 'p', ...[
                            'body1', 'body2', 'body3', '[value1, value2]',
                        ]));

                    });

                });

                describe('->withCallable()', function () {

                    it('should return a new Template with a body part assigning the variable with the given name to the given callable added', function () {

                        $callable = function () {};

                        $this->delegate->compiled
                            ->with(Kahlan\Arg::toBe($callable))
                            ->returns('callable');

                        $test = $this->template->withCallable('variable', $callable);

                        expect($test)->not->toBe($this->template);
                        expect($test)->toEqual(new Template($this->compiler, 'c', 'p', ...[
                            'body1', 'body2', 'body3', '$variable = callable;',
                        ]));

                    });

                });

                describe('->strWithReturn()', function () {

                    it('should return a string representation of a container factory with the container variable name, the previous parameter and the body parts returning the given value', function () {

                        $test = $this->template->strWithReturn('value');

                        expect($test)->toEqual(<<<'EOT'
function (\Psr\Container\ContainerInterface $c, p) {
    body1
    body2
    body3
    return value;
}
EOT
                        );

                    });

                });

                describe('->strWithReturnf()', function () {

                    it('should return a string representation of a container factory with the container variable name, the previous parameter and the body parts returning the value created by applying the given arguments to the given sprintf format', function () {

                        $test = $this->template->strWithReturnf('[%s, %s]', 'value1', 'value2');

                        expect($test)->toEqual(<<<'EOT'
function (\Psr\Container\ContainerInterface $c, p) {
    body1
    body2
    body3
    return [value1, value2];
}
EOT
                        );

                    });


                });

                describe('->__toString()', function () {

                    it('should return the string representation of a factory with the container variable name, the previous parameter and the body parts', function () {

                        expect((string) $this->template)->toEqual(<<<'EOT'
function (\Psr\Container\ContainerInterface $c, p) {
    body1
    body2
    body3
}
EOT
                        );

                    });

                });

            });

        });

    });

});
