<?php

namespace Innocenzi\Vite\Exceptions;

use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\ProvidesSolution;
use Facade\IgnitionContracts\Solution;

final class NoSuchEntrypointException extends ViteException implements ProvidesSolution
{
    protected string $entry;
    protected ?string $configName;

    public function __construct(
        string $entry,
        ?string $configName = null,
        ?string $message = null
    ) {
        $this->entry = $entry;
        $this->configName = $configName;

        $this->message = $message ?? "Entry \"${entry}\" could not be found.";
    }

    public static function inManifest(string $entry, ?string $configName = null): self
    {
        return new self(
            $entry,
            $configName,
            "Entry \"${entry}\" does not exist in the manifest."
        );
    }

    public static function inConfiguration(string $entry, string $configName): self
    {
        return new self(
            $entry,
            $configName,
            "Entry \"${entry}\" could not be found in the configuration."
        );
    }

    public function getSolution(): Solution
    {
        return BaseSolution::create("Add it to your configuration")
            ->setSolutionDescription(
                "That entry point should be defined by the `vite.configs.{$this->getConfigName()}.entrypoints` configuration option."
            )
            ->setDocumentationLinks(
                [
                    'About entrypoints'       => 'https://laravel-vite.dev/guide/usage.html#entrypoints',
                    'Configuring entrypoints' => 'https://laravel-vite.dev/guide/configuration.html#options',
                ]
            );
    }
}
