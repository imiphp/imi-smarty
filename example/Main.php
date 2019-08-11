<?php
namespace Imi\Smarty\Example;

use Imi\Main\AppBaseMain;
use Doctrine\Common\Annotations\AnnotationReader;

class Main extends AppBaseMain
{
    public function __init()
    {
        // 这里可以做一些初始化操作，如果需要的话
        AnnotationReader::addGlobalIgnoredName('testdox');
    }

}