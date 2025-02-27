<?php

namespace Innocenzi\Vite\Exceptions;

use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\ProvidesSolution;
use Facade\IgnitionContracts\Solution;

final class NoBuildPathException extends ViteException implements ProvidesSolution
{
    protected ?string $configName;

    public function __construct(
        ?string $configName = null
    ) {
        $this->configName = $configName;

        $this->message = $this->hasConfigName()
            ? "The build path for the \"{$this->getConfigName()}\" configuration is not defined."
            : "The build path is not defined.";
    }

    public function getSolution(): Solution
    {
        return BaseSolution::create("Add it to your configuration")
            ->setSolutionDescription(
                "The build path should be defined by the `vite.configs.{$this->getConfigName()}.build_path` configuration option.\n"
                . 'This option cannot be empty because the `/public` directory would be emptied.'
            )
            ->setDocumentationLinks([
                'Configuration' => 'https://laravel-vite.dev/guide/essentials/configuration#url',
                'Building for production' => '/guide/essentials/building-for-production',
            ]);
    }
}
