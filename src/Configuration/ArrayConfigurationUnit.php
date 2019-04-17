<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

use Quanta\Container\Tag;
use Quanta\Container\Alias;
use Quanta\Container\Tagging;
use Quanta\Container\Invokable;
use Quanta\Container\Extension;
use Quanta\Container\ValueParser;
use Quanta\Container\TaggingPass;
use Quanta\Container\ExtensionPass;
use Quanta\Container\MergedProcessingPass;
use Quanta\Container\ProcessingPassInterface;

final class ArrayConfigurationUnit implements ConfigurationUnitInterface
{
    /**
     * The parser used to produce factories from parameters.
     *
     * @var \Quanta\Container\ValueParser
     */
    private $parser;

    /**
     * The configuration array.
     *
     * @var array
     */
    private $configuration;

    /**
     * The source of the array.
     *
     * Displayed in exception message when not empty.
     *
     * @var string
     */
    private $source;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\ValueParser $parser
     * @param array                         $configuration
     * @param string                        $source
     */
    public function __construct(ValueParser $parser, array $configuration, string $source = '')
    {
        $this->parser = $parser;
        $this->configuration = $configuration;
        $this->source = $source;
    }

    /**
     * @inheritdoc
     */
    public function factories(): array
    {
        $result = \Quanta\ArrayTypeCheck::nested($this->configuration, [
            'parameters' => '*',
            'aliases' => 'string',
            'invokables' => 'string',
            'factories' => 'callable',
        ]);

        if (! $result->isValid()) {
            throw new \UnexpectedValueException(
                $this->arrayNotValidErrorMessage($result)
            );
        }

        $configuration = $result->sanitized();

        return array_merge(
            array_map($this->parser, $configuration['parameters']),
            array_map(function ($id) {
                return new Alias($id);
            }, $configuration['aliases']),
            array_map(function ($class) {
                return new Invokable($class);
            }, $configuration['invokables']),
            $configuration['factories']
        );
    }

    /**
     * @inheritdoc
     */
    public function pass(): ProcessingPassInterface
    {
        $result = \Quanta\ArrayTypeCheck::nested($this->configuration, [
            'extensions' => 'callable',
            'tags.*' => 'string',
            'mappers' => 'string',
            'passes' => ProcessingPassInterface::class,
        ]);

        if (! $result->isValid()) {
            throw new \UnexpectedValueException(
                $this->arrayNotValidErrorMessage($result)
            );
        }

        $configuration = $result->sanitized();

        return new MergedProcessingPass(...array_merge(
            $this->taggingPasses($configuration['tags']),
            $this->reverseTaggingPasses($configuration['mappers']),
            $this->extensionPasses($configuration['extensions']),
            array_values($configuration['passes'])
        ));
    }

    /**
     * Return an array of tagging passes from the given associative array of tag
     * to array of ids (manual tagging).
     *
     * @param array[] $tags
     * @return \Quanta\Container\TaggingPass[]
     */
    private function taggingPasses(array $tags): array
    {
        return array_map(function ($id, $ids) {
            return new TaggingPass($id, new Tagging\Entries(...array_values($ids)));
        }, array_keys($tags), $tags);
    }

    /**
     * Return an array of tagging passes from the given associative array of tag
     * to interface name (reverse tagging).
     *
     * @param string[] $tags
     * @return \Quanta\Container\TaggingPass[]
     */
    private function reverseTaggingPasses(array $tags): array
    {
        return array_map(function ($id, $class) {
            return new TaggingPass($id, new Tagging\Implementations($class));
        }, array_keys($tags), $tags);
    }

    /**
     * Return an array of extension passes from the given associative array of
     * id to extension.
     *
     * @param callable[] $extensions
     * @return \Quanta\Container\ExtensionPass[]
     */
    private function extensionPasses(array $extensions): array
    {
        return array_map(function ($id, $extension) {
            return new ExtensionPass($id, $extension);
        }, array_keys($extensions), $extensions);
    }

    /**
     * Return the message of exceptions thrown when the given array type check
     * result is not valid.
     *
     * @param \Quanta\ArrayTypeCheck\ResultInterface $result
     */
    private function arrayNotValidErrorMessage(\Quanta\ArrayTypeCheck\ResultInterface $result): string
    {
        $message = $result->message()->source('configuration array');

        return $this->source != ''
            ? sprintf('%s (%s)', $message, $this->source)
            : $message;
    }
}
