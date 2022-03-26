<?php

namespace Innocenzi\Vite\EntrypointsFinder;

use Illuminate\Support\Collection;

interface EntrypointsFinder
{
    /**
     * Finds entrypoints.
     *
     * @param array|string$paths Paths to files or directories that contain entrypoints.
     * @param string|array $ignore Regular expression to match againsts paths.
     */
    public function find($paths, $ignore): Collection;
}
