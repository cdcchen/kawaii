<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace kawaii\web;

use kawaii\base\Object;

/**
 * JsonResponseFormatter formats the given data into a JSON or JSONP response content.
 *
 * It is used by [[Response]] to format response data.
 */
class JsonResponseFormatter extends Object implements ResponseFormatterInterface
{
    /**
     * @var boolean whether to use JSONP response format. When this is true, the [[Response::data|response data]]
     * must be an array consisting of `data` and `callback` members. The latter should be a JavaScript
     * function name while the former will be passed to this function as a parameter.
     */
    public $useJsonp = false;
    /**
     * @var integer the encoding options passed to [[Json::encode()]]. For more details please refer to
     * <http://www.php.net/manual/en/function.json-encode.php>.
     * Default is `JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE`.
     * This property has no effect, when [[useJsonp]] is `true`.
     * @since 2.0.7
     */
    public $encodeOptions = 320;
    /**
     * @var bool whether to format the output in a readable "pretty" format. This can be useful for debugging purpose.
     * If this is true, `JSON_PRETTY_PRINT` will be added to [[encodeOptions]].
     * Defaults to `false`.
     * This property has no effect, when [[useJsonp]] is `true`.
     * @since 2.0.7
     */
    public $prettyPrint = false;


    /**
     * Formats the specified response.
     * @param \kawaii\web\Response $response the response to be formatted.
     * @return \kawaii\web\Response
     */
    public function format($response)
    {
        return $this->useJsonp ? $this->formatJsonp($response) : $this->formatJson($response);
    }

    /**
     * Formats response data in JSON format.
     * @param \kawaii\web\Response $response
     * @return \kawaii\web\Response
     */
    protected function formatJson($response)
    {
        $response = $response->withHeader('Content-Type', 'application/json; charset=UTF-8');
        if ($response->getData() !== null) {
            $options = $this->encodeOptions;
            if ($this->prettyPrint) {
                $options |= JSON_PRETTY_PRINT;
            }
            $response->content = json_encode($response->getData(), $options);
        }

        return $response;
    }

    /**
     * Formats response data in JSONP format.
     * @param \kawaii\web\Response $response
     * @return \kawaii\web\Response
     */
    protected function formatJsonp($response)
    {
        $response = $response->withHeader('Content-Type', 'application/javascript; charset=UTF-8');
        $data = $response->getData();
        if (is_array($data) && isset($data['data'], $data['callback'])) {
            $content = sprintf('%s(%s);', $data['callback'], json_encode($data['data']));
            $response->getBody()->write($content);
        } elseif ($data !== null) {
            $response->getBody()->write('');
        }

        return $response;
    }
}
