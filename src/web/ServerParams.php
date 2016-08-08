<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/7/27
 * Time: 20:50
 */

namespace kawaii\web;


use kawaii\Kawaii;

class ServerParams
{
    public static function create()
    {
        return [
            'USER' => '',
            'HOME' => '',
            'REDIRECT_STATUS' => '',
            'SERVER_NAME' => '',
            'SERVER_ADDR' => '',
            'SERVER_PORT' => '',
            'REMOTE_PORT' => '',
            'REMOTE_ADDR' => '',
            'SERVER_SOFTWARE' => Kawaii::$version,
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'DOCUMENT_ROOT' => '/' . $_SERVER['DOCUMENT_ROOT'],
            'DOCUMENT_URI' => '/' . $_SERVER['SCRIPT_NAME'],
            'REQUEST_URI' => '/' . $_SERVER['SCRIPT_NAME'],
            'SCRIPT_FILENAME' => '/' . $_SERVER['SCRIPT_FILENAME'],
            'SCRIPT_NAME' => '/' . $_SERVER['SCRIPT_NAME'],
//            'CONTENT_LENGTH' => $request->getHeaderLine('Content-Length'),
//            'CONTENT_TYPE' => $request->getHeaderLine('Content-Type'),
//            'QUERY_STRING' => $request->getUri()->getQuery(),
//            'REQUEST_METHOD' => $request->getMethod(),
            'PHP_SELF' => '/' . $_SERVER['PHP_SELF'],
            'REQUEST_TIME' => time(),
            'REQUEST_TIME_FLOAT' => microtime(true),
        ];
    }
}