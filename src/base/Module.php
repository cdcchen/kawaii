<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/8/2
 * Time: 15:47
 */

namespace kawaii\base;


class Module extends Object
{
    public $params        = [];
    public $id;
    public $module;
    public $layout;
    public $controllerMap = [];
    public $controllerNamespace;
    public $defaultRoute  = 'default';

    private $basePath;
    private $viewPath;
    private $layoutPath;
    private $modules = [];

//    public function __construct($id, $parent = null, $config = [])
//    {
//        $this->id = $id;
//        $this->module = $parent;
//        parent::__construct($config);
//    }


}