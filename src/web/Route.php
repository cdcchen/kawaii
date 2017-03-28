<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/7/30
 * Time: 17:08
 */

namespace kawaii\web;


/**
 * Class Route
 * @package kawaii\web
 */
class Route
{
    /**
     * @var string
     */
    public $method;
    /**
     * @var array
     */
    public $data = [];
    /**
     * @var callable
     */
    public $handler;
    /**
     * @var bool
     */
    public $strict = false;
    /**
     * @var string
     */
    public $suffix = '';

    /**
     * @var string
     */
    private $regex;
    /**
     * @var array
     */
    private $varNames = [];

    /**
     * Route constructor.
     * @param string $method
     * @param callable $handler
     * @param array $data
     * @param bool $strict
     * @param string $suffix
     */
    public function __construct($method, callable $handler, array $data, bool $strict = false, string $suffix = '')
    {
        $this->method = strtoupper($method);
        $this->handler = $handler;
        $this->data = $data;
        $this->strict = (bool)$strict;
        $this->suffix = (string)$suffix;

        if (!$this->isStatic()) {
            [$this->regex, $this->varNames] = static::buildRegexForRoute($data);
        }
    }

    /**
     * @param string $path
     * @return bool
     */
    public function match(string $path): bool
    {
        return (bool)preg_match($this->getRegex(), $path);
    }

    /**
     * @return string|null
     */
    public function getRegex(): ?string
    {
        return $this->strict ? ('^' . $this->regex . '$') : $this->regex;
    }

    /**
     * @return array
     */
    public function getVarNames(): array
    {
        return $this->varNames ?: [];
    }

    /**
     * @return bool
     */
    public function isStatic(): bool
    {
        return count($this->data) === 1 && is_string($this->data[0]);
    }

    /**
     * @param array $data
     * @return array
     * @throws BadRouteException
     */
    private static function buildRegexForRoute(array $data): array
    {
        $regex = '';
        $variables = [];
        foreach ($data as $part) {
            if (is_string($part)) {
                $regex .= preg_quote($part, '~');
                continue;
            }

            list($varName, $regexPart) = $part;
            if (isset($variables[$varName])) {
                throw new BadRouteException(sprintf('Cannot use the same placeholder "%s" twice', $varName));
            }

            if (static::regexHasCapturingGroups($regexPart)) {
                throw new BadRouteException(sprintf('Regex "%s" for parameter "%s" contains a capturing group',
                    $regexPart, $varName));
            }

            $variables[$varName] = $varName;
            $regex .= '(' . $regexPart . ')';
        }

        return [$regex, $variables];
    }

    /**
     * @param string $regex
     * @return bool|int
     */
    private static function regexHasCapturingGroups(string $regex)
    {
        if (false === strpos($regex, '(')) {
            // Needs to have at least a ( to contain a capturing group
            return false;
        }
        // Semi-accurate detection for capturing groups
        return preg_match(
            '~
                (?:
                    \(\?\(
                  | \[ [^\]\\\\]* (?: \\\\ . [^\]\\\\]* )* \]
                  | \\\\ .
                ) (*SKIP)(*FAIL) |
                \(
                (?!
                    \? (?! <(?![!=]) | P< | \' )
                  | \*
                )
            ~x',
            $regex
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'method' => $this->method,
            'data' => $this->data,
            'regex' => $this->regex,
            'varNames' => $this->varNames,
        ];
    }
}