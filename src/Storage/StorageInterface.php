<?php

namespace burtwraynolds\CriticalCss\Storage;

/**
 * This handles reading and writing critical-path CSS from disk.
 */
interface StorageInterface
{
    /**
     * Returns generated critical-path CSS for the given URI.
     *
     * @param  string $uri
     *
     * @return string
     *
     * @throws \burtwraynolds\CriticalCss\Storage\CssReadException
     */
    public function readCss($uri);

    /**
     * Write generated critical-path CSS for a given URI for later use.
     *
     * @param  string $uri
     * @param  string $css
     *
     * @return bool
     *
     * @throws \burtwraynolds\CriticalCss\Storage\CssWriteException
     */
    public function writeCss($uri, $css);
}
