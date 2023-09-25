<?php

namespace Laravel\Pail;

use Closure;

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
     * Runs the given callback for each tailed file.
     */
    public function each(Closure $callback): void
    {
        $files = glob($this->path.'/*.pail');

        if (is_array($files)) {
            foreach ($files as $file) {
                $callback(new TailedFile($file));
            }
        }
    }
}
