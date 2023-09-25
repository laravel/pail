<?php

namespace Laravel\Pail;

use Illuminate\Support\Collection;

class TailedFiles
{
    /**
     * Creates a new instance of the tailed files.
     */
    public function __construct(
        protected string $path,
    ) {
        //
    }

    /**
     * Returns the list of tailed files.
     *
     * @return \Illuminate\Support\Collection<int, TailedFile>
     */
    public function all(): Collection
    {
        $files = glob($this->path.'/*.pail') ?: [];

        return collect($files)
            ->map(fn (string $file) => new TailedFile($file));
    }
}
