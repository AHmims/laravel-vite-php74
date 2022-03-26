<?php

namespace Innocenzi\Vite\TagGenerators;

use Innocenzi\Vite\Chunk;
use Innocenzi\Vite\Vite;

final class CallbackTagGenerator implements TagGenerator
{
    protected DefaultTagGenerator $tagGenerator;

    public function __construct(DefaultTagGenerator $tagGenerator)
    {
        $this->tagGenerator = $tagGenerator;
    }

    public function makeScriptTag(string $url, Chunk $chunk = null): string
    {
        if (\is_callable(Vite::$makeScriptTagsCallback)) {
            return \call_user_func(Vite::$makeScriptTagsCallback, $url, $chunk);
        }

        return $this->tagGenerator->makeScriptTag($url, $chunk);
    }

    public function makeStyleTag(string $url, Chunk $chunk = null): string
    {
        if (\is_callable(Vite::$makeStyleTagsCallback)) {
            return \call_user_func(Vite::$makeStyleTagsCallback, $url, $chunk);
        }

        return $this->tagGenerator->makeStyleTag($url, $chunk);
    }
}
