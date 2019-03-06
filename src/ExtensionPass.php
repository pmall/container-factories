<?php declare(strict_types=1);

namespace Quanta\Container;

use Quanta\Container\Factories\Extension;

final class ExtensionPass implements ProcessingPassInterface
{
    /**
     * The id of the container entry to extend.
     *
     * @var string
     */
    private $id;

    /**
     * The extension.
     *
     * @var callable
     */
    private $extension;

    /**
     * Return a new ExtensionPass with the given id and extension.
     *
     * @param string    $id
     * @param callable  $extension
     * @return \Quanta\Container\ExtensionPass
     */
    public static function instance(string $id, callable $extension): ExtensionPass
    {
        return new self($id, $extension);
    }

    /**
     * Constructor.
     *
     * @param string    $id
     * @param callable  $extension
     */
    public function __construct(string $id, callable $extension)
    {
        $this->id = $id;
        $this->extension = $extension;
    }

    /**
     * @inheritdoc
     */
    public function processed(array $factories): array
    {
        if (key_exists($this->id, $factories)) {
            $factories[$this->id] = new Extension(
                $factories[$this->id],
                $this->extension
            );
        }

        return $factories;
    }
}
