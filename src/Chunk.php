<?php

namespace Innocenzi\Vite;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Innocenzi\Vite\TagGenerators\TagGenerator;

final class Chunk implements Htmlable
{
    protected TagGenerator $tagGenerator;

    public Manifest $manifest;

    public string $file;

    public ?string $src;

    public bool $isEntry;

    public bool $isDynamicEntry;

    public Collection $css;

    public Collection $imports;

    public Collection $dynamicImports;

    public Collection $assets;

    public ?string $integrity;

    public function __construct(
        Manifest $manifest,
        string $file,
        $src,
        bool $isEntry,
        bool $isDynamicEntry,
        Collection $css,
        Collection $imports,
        Collection $dynamicImports,
        Collection $assets,
        $integrity = null
    ) {
        $this->manifest = $manifest;
        $this->file = $file;
        $this->src = $src;
        $this->isEntry = $isEntry;
        $this->isDynamicEntry = $isDynamicEntry;
        $this->css = $css;
        $this->imports = $imports;
        $this->dynamicImports = $dynamicImports;
        $this->assets = $assets;
        $this->integrity = $integrity;

        $this->tagGenerator = app(TagGenerator::class);
    }

    /**
     * Generates a manifest entry from an array.
     */
    public static function fromArray(Manifest $manifest, array $manifestEntry): self
    {
        return new Chunk(
            $manifest,
            $manifestEntry['file'] ?? '',
            $manifestEntry['src'] ?? null,
            $manifestEntry['isEntry'] ?? false,
            $manifestEntry['isDynamicEntry'] ?? false,
            collect($manifestEntry['css'] ?? []),
            collect($manifestEntry['imports'] ?? []),
            collect($manifestEntry['dynamicImports'] ?? []),
            collect($manifestEntry['assets'] ?? []),
            $manifestEntry['integrity'] ?? null
        );
    }

    /**
     * Gets the tag for this entry.
     */
    public function getTag(): string
    {
        // If the file is a CSS file, the main tag is a style tag.
        if (Str::endsWith($this->file, '.css')) {
            return $this->tagGenerator->makeStyleTag($this->getAssetUrl($this->file), $this);
        }

        // Otherwise, it's a script tag.
        return $this->tagGenerator->makeScriptTag($this->getAssetUrl($this->file), $this);
    }

    /**
     * Gets the style tags for this entry.
     */
    public function getStyleTags(): Collection
    {
        return $this->css->map(fn(string $path) => $this->tagGenerator->makeStyleTag($this->getAssetUrl($path)));
    }

    /**
     * Gets every script and style tag.
     */
    public function getTags(): Collection
    {
        return collect()
            ->push($this->getTag())
            ->push(...$this->getStyleTags());
    }

    /**
     * Gets the complete path for the given asset path.
     */
    protected function getAssetUrl(string $path): string
    {
        // Determines the base path from the manifest path
        $public = str_replace('\\', '/', public_path());
        $base = str_replace($public, '', $this->manifest->getPath());
        $base = \dirname($base);
        $base = Str::of($base)
            ->replace('\\', '/')
            ->finish('/');

        return asset(sprintf('%s%s', $base, $path));
    }

    public function toHtml()
    {
        return $this->getTags()->join('');
    }

    public function __toString(): string
    {
        return $this->toHtml();
    }
}
