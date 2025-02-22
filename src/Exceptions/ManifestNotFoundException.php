<?php

namespace Innocenzi\Vite\Exceptions;

use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\ProvidesSolution;
use Facade\IgnitionContracts\Solution;

final class ManifestNotFoundException extends ViteException implements ProvidesSolution
{
    protected array $links = [
        'About development' => 'https://laravel-vite.dev/guide/essentials/development',
        'Building for production' => 'https://laravel-vite.dev/guide/essentials/building-for-production',
    ];

    protected string $manifestPath;
    protected ?string $configName;

    public function __construct(
        string $manifestPath,
        ?string $configName = null
    ) {
        $this->manifestPath = $manifestPath;
        $this->configName = $configName;


        $this->message = !$this->hasConfigName()
            ? "The manifest could not be found."
            : "The manifest for the \"{$this->getConfigName()}\" configuration could not be found.";
    }

    public function getSolution(): Solution
    {
        $baseCommand = collect([
                                   'pnpm-lock.yaml' => 'pnpm',
                                   'yarn.lock'      => 'yarn',
                               ])->reduce(function ($default, $command, $lockFile) {
            if (file_exists(base_path($lockFile))) {
                return $command;
            }

            return $default;
        }, 'npm run');

        return app()->environment('local')
            ? $this->getLocalSolution($baseCommand)
            : $this->getProductionSolution($baseCommand);
    }

    protected function getLocalSolution(string $baseCommand): Solution
    {
        return BaseSolution::create('Start the development server')
            ->setSolutionDescription(
                "Run `{$this->getCommand($baseCommand, 'dev')}` in your terminal and refresh the page."
            )
            ->setDocumentationLinks($this->links);
    }

    protected function getProductionSolution(string $baseCommand): Solution
    {
        return BaseSolution::create('Build the production assets')
            ->setSolutionDescription(
                "Run `{$this->getCommand($baseCommand, 'build')}` in your terminal and refresh the page."
            )
            ->setDocumentationLinks($this->links);
    }

    protected function getCommand(string $baseCommand, string $type): string
    {
        $command = "${baseCommand} ${type}";

        if ($this->hasConfigName()) {
            $command .= " --config vite.{$this->getConfigName()}.config.ts";
        }

        return $command;
    }
}
