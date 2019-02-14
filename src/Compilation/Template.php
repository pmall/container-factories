<?php declare(strict_types=1);

namespace Quanta\Container\Compilation;

use Psr\Container\ContainerInterface;

final class Template
{
    /**
     * The callable compiler.
     *
     * @var \Quanta\Container\Compilation\CallableCompiler
     */
    private $compiler;

    /**
     * The container variable name to use.
     *
     * Default to 'container'
     *
     * @var string
     */
    private $container;

    /**
     * The previous parameter string representation.
     *
     * Default to ''
     *
     * @var string
     */
    private $previous;

    /**
     * The factory body parts.
     *
     * @var string[]
     */
    private $parts;

    /**
     * Return a new Tmeplate with a dummy closure compiler (testin purpose).
     *
     * @param string $container
     * @return \Quanta\Container\Compilation\Template
     */
    public static function withDummyClosureCompiler(string $container = 'container'): Template
    {
        return new Template(
            CallableCompiler::withDummyClosureCompiler(),
            $container
        );
    }

    /**
     * Return a new Template with the given closure compiler.
     *
     * @param \Quanta\Container\Compilation\ClosureCompilerInterface    $compiler
     * @param string                                                    $container
     * @return \Quanta\Container\Compilation\Template
     */
    public static function withClosureCompiler(ClosureCompilerInterface $compiler, string $container = 'container')
    {
        return new Template(
            new CallableCompiler($compiler),
            $container
        );
    }

    /**
     * Constructor.
     *
     * @param \Quanta\Container\Compilation\CallableCompiler    $compiler
     * @param string                                            $container
     * @param string                                            $previous
     * @param string                                            ...$parts
     */
    public function __construct(
        CallableCompiler $compiler,
        string $container = 'container',
        string $previous = '',
        string ...$parts
    ) {
        $this->compiler = $compiler;
        $this->container = $container;
        $this->previous = $previous;
        $this->parts = $parts;
    }

    /**
     * Return the container variable name.
     *
     * @return string
     */
    public function containerVariableName(): string
    {
        return $this->container;
    }

    /**
     * Return a new Template with the given previous parameter string
     * representation.
     *
     * @param string $previous
     * @return \Quanta\Container\Compilation\Template
     */
    public function withPrevious(string $previous): Template
    {
        return new Template(
            $this->compiler,
            $this->container,
            $previous,
            ...$this->parts
        );
    }

    /**
     * Return a new Template with the given body part.
     *
     * @param string ...$parts
     * @return \Quanta\Container\Compilation\Template
     */
    public function withBody(string ...$parts): Template
    {
        return new Template(
            $this->compiler,
            $this->container,
            $this->previous,
            ...$this->parts,
            ...$parts
        );
    }

    /**
     * Return a new Template with the a body part created by applying the given
     * arguments to the given sprintf format.
     *
     * @param string    $format
     * @param mixed     ...$xs
     * @return \Quanta\Container\Compilation\Template
     */
    public function withBodyf(string $format, ...$xs): Template
    {
        return $this->withBody(sprintf($format, ...$xs));
    }

    /**
     * Return a new Template with assigning the variable with the given name to
     * the given callable.
     *
     * @param string    $variable
     * @param callable  $callable
     * @return \Quanta\Container\Compilation\Template
     */
    public function withCallable(string $variable, callable $callable): Template
    {
        $compiled = $this->compiler->compiled($callable);

        return $this->withBodyf('$%s = %s;', $variable, $compiled);
    }

    /**
     * Return the string representation of the factory returning the given
     * value.
     *
     * @param string $return
     * @return string
     */
    public function strWithReturn(string $return): string
    {
        return (string) $this->withBody(sprintf('return %s;', $return));
    }

    /**
     * Return the string representation of the factory returning the value
     * created by applying the given arguments to the given sprintf format.
     *
     * @param string    $format
     * @param mixed     ...$xs
     * @return string
     */
    public function strWithReturnf(string $format, ...$xs): string
    {
        return $this->strWithReturn(sprintf($format, ...$xs));
    }

    /**
     * Return a string representation of the factory.
     *
     * @return string
     */
    public function __toString()
    {
        $body = count($this->parts) > 0
            ? implode(PHP_EOL, $this->parts)
            : '//';

        $xs[] = ContainerInterface::class;
        $xs[] = $this->container;
        if ($this->previous != '') $xs[] = $this->previous;
        $xs[] = PHP_EOL;
        $xs[] = new IndentedStr($body);
        $xs[] = PHP_EOL;

        $tpl = $this->previous == ''
            ? 'function (\%s $%s) {%s%s%s}'
            : 'function (\%s $%s, %s) {%s%s%s}';

        return vsprintf($tpl, $xs);
    }
}
