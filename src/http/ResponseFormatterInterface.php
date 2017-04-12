<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace kawaii\http;

/**
 * ResponseFormatterInterface specifies the interface needed to format a response before it is sent out.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
interface ResponseFormatterInterface
{
    /**
     * Formats the specified response.
     * @param \kawaii\http\Response $response the response to be formatted.
     * @return \kawaii\http\Response
     */
    public function format($response);
}
