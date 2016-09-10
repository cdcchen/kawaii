<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/8/24
 * Time: 09:54
 */

namespace kawaii\http;


/**
 * Interface HTTP
 * @package kawaii\http
 */
interface HTTP
{
    const METHOD_GET     = 'GET';
    const METHOD_HEAD    = 'HEAD';
    const METHOD_POST    = 'POST';
    const METHOD_PUT     = 'PUT';
    const METHOD_PATCH   = 'PATCH'; // RFC 5789
    const METHOD_DELETE  = 'DELETE';
    const METHOD_CONNECT = 'CONNECT';
    const METHOD_OPTIONS = 'OPTIONS';
    const METHOD_TRACE   = 'TRACE';


    const STATUS_CONTINUE               = 100; // RFC 7231, 6.2.1
    const STATUS_SWITCHING_PROTOCOLS    = 101; // RFC 7231, 6.2.2
    const STATUS_PROCESSING             = 102; // RFC 2518, 10.1

    const STATUS_OK                     = 200; // RFC 7231, 6.3.1
    const STATUS_CREATED                = 201; // RFC 7231, 6.3.2
    const STATUS_ACCEPTED               = 202; // RFC 7231, 6.3.3
    const STATUS_NON_AUTHORITATIVE_INFO = 203; // RFC 7231, 6.3.4
    const STATUS_NO_CONTENT             = 204; // RFC 7231, 6.3.5
    const STATUS_RESET_CONTENT          = 205; // RFC 7231, 6.3.6
    const STATUS_PARTIAL_CONTENT        = 206; // RFC 7233, 4.1
    const STATUS_MULTI_STATUS_          = 207; // RFC 4918, 11.1
    const STATUS_ALREADY_REPORTED       = 208; // RFC 5842, 7.1
    const STATUS_IM_USED                = 226; // RFC 3229, 10.4.1

    const STATUS_MULTIPLE_CHOICES       = 300; // RFC 7231, 6.4.1
    const STATUS_MOVED_PERMANENTLY      = 301; // RFC 7231, 6.4.2
    const STATUS_FOUND                  = 302; // RFC 7231, 6.4.3
    const STATUS_SEE_OTHER              = 303; // RFC 7231, 6.4.4
    const STATUS_NOT_MODIFIED           = 304; // RFC 7232, 4.1
    const STATUS_USE_PROXY              = 305; // RFC 7231, 6.4.5
    const STATUS_TEMPORARY_REDIRECT     = 307; // RFC 7231, 6.4.7
    const STATUS_PERMANENT_REDIRECT     = 308; // RFC 7538, 3

    const STATUS_BAD_REQUEST                        = 400; // RFC 7231, 6.5.1
    const STATUS_UNAUTHORIZED                       = 401; // RFC 7235, 3.1
    const STATUS_PAYMENT_REQUIRED                   = 402; // RFC 7231, 6.5.2
    const STATUS_FORBIDDEN                          = 403; // RFC 7231, 6.5.3
    const STATUS_NOT_FOUND                          = 404; // RFC 7231, 6.5.4
    const STATUS_METHOD_NOT_ALLOWED                 = 405; // RFC 7231, 6.5.5
    const STATUS_NOT_ACCEPTABLE                     = 406; // RFC 7231, 6.5.6
    const STATUS_PROXY_AUTH_REQUIRED                = 407; // RFC 7235, 3.2
    const STATUS_REQUEST_TIMEOUT                    = 408; // RFC 7231, 6.5.7
    const STATUS_CONFLICT                           = 409; // RFC 7231, 6.5.8
    const STATUS_GONE                               = 410; // RFC 7231, 6.5.9
    const STATUS_LENGTH_REQUIRED                    = 411; // RFC 7231, 6.5.10
    const STATUS_PRECONDITION_FAILED                = 412; // RFC 7232, 4.2
    const STATUS_REQUEST_ENTITY_TOO_LARGE           = 413; // RFC 7231, 6.5.11
    const STATUS_REQUEST_URI_TOOLONG                = 414; // RFC 7231, 6.5.12
    const STATUS_UNSUPPORTED_MEDIA_TYPE             = 415; // RFC 7231, 6.5.13
    const STATUS_REQUESTED_RANGENOT_SATISFIABLE     = 416; // RFC 7233, 4.4
    const STATUS_EXPECTATION_FAILED                 = 417; // RFC 7231, 6.5.14
    const STATUS_TEAPOT                             = 418; // RFC 7168, 2.3.3
    const STATUS_UNPROCESSABLE_ENTITY               = 422; // RFC 4918, 11.2
    const STATUS_LOCKED                             = 423; // RFC 4918, 11.3
    const STATUS_FAILED_DEPENDENCY                  = 424; // RFC 4918, 11.4
    const STATUS_UPGRADE_REQUIRED                   = 426; // RFC 7231, 6.5.15
    const STATUS_PRECONDITION_REQUIRED              = 428; // RFC 6585, 3
    const STATUS_TOO_MANY_REQUESTS                  = 429; // RFC 6585, 4
    const STATUS_REQUEST_HEADER_FIELDS_TOO_LARGE    = 431; // RFC 6585, 5
    const STATUS_UNAVAILABLE_FOR_LEGAL_REASONS      = 451; // RFC 7725, 3

    const STATUS_INTERNAL_SERVER_ERROR              = 500; // RFC 7231, 6.6.1
    const STATUS_NOT_IMPLEMENTED                    = 501; // RFC 7231, 6.6.2
    const STATUS_BAD_GATEWAY                        = 502; // RFC 7231, 6.6.3
    const STATUS_SERVICE_UNAVAILABLE                = 503; // RFC 7231, 6.6.4
    const STATUS_GATEWAY_TIMEOUT                    = 504; // RFC 7231, 6.6.5
    const STATUS_HTTP_VERSION_NOT_SUPPORTED         = 505; // RFC 7231, 6.6.6
    const STATUS_VARIANT_ALSO_NEGOTIATES            = 506; // RFC 2295, 8.1
    const STATUS_INSUFFICIENT_STORAGE               = 507; // RFC 4918, 11.5
    const STATUS_LOOP_DETECTED                      = 508; // RFC 5842, 7.2
    const STATUS_NOT_EXTENDED                       = 510; // RFC 2774, 7
    const STATUS_NETWORK_AUTHENTICATION_REQUIRED    = 511; // RFC 6585, 6

    const HEAD_MAX_LENGTH = 8192;
}