<?php declare(strict_types=1);

namespace Quanta\Container\Compilation;

final class Compiler
{
    /**
     * The closure compiler.
     *
     * @var \Quanta\Container\Compilation\ClosureCompilerInterface
     */
    private $compiler;

    /**
     * The array of compiled value to value pairs.
     *
     * @var array
     */
    private $precompiled;

    /**
     * Return a new Compiler using a dummy closure compiler and the given
     * precompiled array.
     *
     * @param array $precompiled
     * @return \Quanta\Container\Compilation\Compiler
     */
    public static function testing(array $precompiled = []): Compiler
    {
        return new Compiler(new DummyClosureCompiler, $precompiled);
    }

    /**
     * Constructor.
     *
     * @param \Quanta\Container\Compilation\ClosureCompilerInterface    $compiler
     * @param array                                                     $precompiled
     */
    public function __construct(ClosureCompilerInterface $compiler, array $precompiled = [])
    {
        $this->compiler = $compiler;
        $this->precompiled = $precompiled;
    }

    /**
     * Return a string representation of the given value.
     *
     * @param mixed $value
     * @return string
     * @throws \InvalidArgumentException
     */
    public function __invoke($value): string
    {
        $precompiled = array_search($value, $this->precompiled, true);

        if ($precompiled !== false) {
            return $precompiled;
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_int($value)) {
            return (string) $value;
        }

        if (is_float($value)) {
            return (string) $value;
        }

        if (is_string($value)) {
            return is_callable($value) ? strval($value) : $this->quoted($value);
        }

        if (is_array($value)) {
            if (! is_callable($value)) {
                return $this->compiledArray($value);
            }

            if (is_string($value[0])) {
                return vsprintf('[%s::class, \'%s\']', $value);
            }

            throw new \InvalidArgumentException(
                vsprintf('Unable to compile callable [object(%s), \'%s\'], please use a factory instead', [
                    $this->classname($value[0]),
                    $value[1],
                ])
            );
        }

        if (is_object($value)) {
            if ($value instanceof CompilableInterface) {
                return $value->compiled($this);
            }

            if ($value instanceof \Closure) {
                return ($this->compiler)($value);
            }

            throw new \InvalidArgumentException(
                vsprintf('Unable to compile object(%s), please use a factory instead', [
                    $this->classname($value),
                ])
            );
        }

        if (is_resource($value)) {
            throw new \InvalidArgumentException(
                'Unable to compile resources, please use a factory instead'
            );
        }

        if (is_null($value)) {
            return 'null';
        }

        throw new \InvalidArgumentException('Unknown type');
    }

    /**
     * Return the class name of the given object.
     *
     * @param object $object
     * @return string
     */
    private function classname($object): string
    {
        $class = get_class($object);

        return strpos($class, 'class@anonymous') !== false
            ? 'class@anonymous'
            : $class;
    }

    /**
     * Return the given string with quotes.
     *
     * @param string $str
     * @return string
     */
    public function quoted(string $str): string
    {
        return '\'' . $str . '\'';
    }

    /**
     * Return a string representation of the given array key => value pair.
     *
     * @param int|string    $key
     * @param mixed         $value
     * @return string
     */
    private function compiledKvPair($key, $value): string
    {
        return vsprintf('%s => %s', [
            is_int($key) ? $key : $this->quoted($key),
            $this($value)
        ]);
    }

    /**
     * Return a string representation of the given array.
     *
     * @param array $arr
     * @return string
     */
    public function compiledArray(array $arr): string
    {
        if (count($arr) > 0) {
            $keys = array_keys($arr);

            $strs = $keys !== range(0, count($keys) - 1)
                ? array_map([$this, 'compiledKvPair'], $keys, $arr)
                : array_map($this, $arr);

            return vsprintf('[%s%s%s]', [
                PHP_EOL,
                new IndentedString(implode(',' . PHP_EOL, $strs) . ','),
                PHP_EOL,
            ]);
        }

        return '[]';
    }
}
