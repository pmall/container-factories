<?php declare(strict_types=1);

namespace Quanta\Container\Formatting;

final class ObjectCompilationErrorMessage
{
    /**
     * The object.
     *
     * @var object
     */
    private $object;

    /**
     * Constructor.
     *
     * @param object $object
     */
    public function __construct($object)
    {
        $this->object = $object;
    }

    /**
     * The error message to display when trying to compile the object.
     *
     * @return string
     */
    public function __toString()
    {
        $class = get_class($this->object);

        return vsprintf('Unable to compile object(%s), please use a factory instead', [
            strpos($class, 'class@anonymous') === false ? $class : 'class@anonymous',
        ]);
    }
}
