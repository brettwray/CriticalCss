<?php

class ConfigTest extends TestCase
{
    protected static $configKeys = [
        'routes'          => 'array',
        'css'             => 'array',
        'width'           => 'integer',
        'height'          => 'integer',
        'ignore'          => 'array',
        'storage'         => 'string',
        'pretend'         => 'string',
        'blade_directive' => 'bool',
        'critical_bin'    => 'string',
        'timeout'         => 'integer',
    ];

    public function testConfigIsOk()
    {
        $path = realpath(__DIR__.'/../src/config/criticalcss.php');

        $this->assertFileExists($path);

        $value = require $path;

        $this->assertIsArray($value);

        foreach (static::$configKeys as $key => $type) {
            $this->assertArrayHasKey($key, $value);

            $this->assertSame($type, $value[$key]);
        }
    }
}

function environment($a, $b = null)
{
    return $a;
}

function base_path($a = '')
{
    return $a;
}
