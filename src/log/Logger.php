<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/8/7
 * Time: 19:33
 */

namespace kawaii\log;


use kawaii\base\Object;
use Psr\Log\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;

class Logger extends Object implements LoggerInterface
{
    use LoggerTrait;

    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    public function log($level, $message, array $context = [])
    {
        $class = new \ReflectionClass('\Psr\Log\LogLevel');
        $constants = $class->getConstants();
        if (in_array($level, $constants)) {
            throw new InvalidArgumentException('Invalid log level.');
        }

        $message = static::interpolate($message, $context);
    }

    private static function interpolate($message, array $context = [])
    {
        $replace = [];
        foreach ($context as $key => $val) {
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
            }
        }

        return strtr($message, $replace);
    }
}