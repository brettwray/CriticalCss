<?php

namespace burtwraynolds\CriticalCss\CssGenerators;

use Symfony\Component\Process\Process;
use burtwraynolds\CriticalCss\Storage\StorageInterface;
use burtwraynolds\CriticalCss\HtmlFetchers\HtmlFetcherInterface;

/**
 * Generates critical-path CSS using the Critical npm package.
 *
 * @see https://github.com/addyosmani/critical
 */
class CriticalGenerator implements CssGeneratorInterface
{
    /** @var array */
    protected $css;

    /** @var \burtwraynolds\CriticalCss\HtmlFetchers\HtmlFetcherInterface */
    protected $htmlFetcher;

    /** @var \burtwraynolds\CriticalCss\Storage\StorageInterface */
    protected $storage;

    /** @var string */
    protected $criticalBin = 'critical';

    /** @var int */
    protected $width;

    /** @var int */
    protected $height;

    /** @var array */
    protected $ignore;

    /** @var int|null */
    protected $timeout;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $css,
                                HtmlFetcherInterface $htmlFetcher,
                                StorageInterface $storage)
    {
        $this->css         = $css;
        $this->htmlFetcher = $htmlFetcher;
        $this->storage     = $storage;
    }

    /**
     * Set the path to the Critical bin (executable.)
     *
     * @param  string $critical
     *
     * @return void
     */
    public function setCriticalBin($critical)
    {
        $this->criticalBin = $critical;
    }

    /**
     * Set optional options for Critical.
     *
     * @param  int      $width
     * @param  int      $height
     * @param  array    $ignore
     * @param  int|null $timeout
     *
     * @return void
     */
    public function setOptions($width = 900, $height = 1300, array $ignore = [], $timeout = null)
    {
        $this->width  = $width;
        $this->height = $height;
        $this->ignore = $ignore;
        $this->timeout = $timeout;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($uri, $alias = null)
    {
        $html = $this->htmlFetcher->fetch($uri);

        $args = array($this->criticalBin,'--base='.realpath(__DIR__.'/../.tmp'), '--width='.$this->width, '--height='.$this->height, '--minify',);

        if (!is_null($this->timeout)) {
            array_push($args, '--timeout='.$this->timeout);
        }

        foreach ($this->css as $css) {
            array_push($args, '--css='.$css);
        }

        foreach ($this->ignore as $ignore) {
            array_push($args, '--ignore='.$ignore);
        }

        $builder = new Process($args);

        if (!is_null($this->timeout)) {
            $builder->setTimeout($this->timeout);
        }

        $builder->setInput($html);

        $builder->run();

        if (!$builder->isSuccessful()) {
            throw new CssGeneratorException(
                sprintf('Error processing URI [%s]. This is probably caused by '.
                        'the Critical npm package. Checklist: 1) `critical_bin`'.
                        ' is correct, 2) `css` paths are correct 3) run `npm '.
                        'install` again.', $uri)
            );
        }

        return $this->storage->writeCss(
            is_null($alias) ? $uri : $alias,
            $builder->getOutput()
        );
    }
}
