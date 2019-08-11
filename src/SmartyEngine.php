<?php
namespace Imi\Smarty;

use Imi\Server\View\Engine\IEngine;
use Imi\Server\Http\Message\Response;
use Imi\Util\Imi;
use Imi\RequestContext;
use Imi\Event\Event;

/**
 * Smarty 模版引擎
 */
class SmartyEngine implements IEngine
{
    /**
     * Smarty 实例列表
     *
     * @var array
     */
    private static $instances = [];
    
    protected $cacheDir;

    protected $compileDir;

    protected $caching;

    protected $cacheLifetime;

    public function render(Response $response, $fileName, $data = []): Response
    {
        $smarty = $this->newSmartyInstance($response->getServerInstance()->getName());
        $smarty->assign($data);
        if(!is_file($fileName))
        {
            return $response;
        }
        $content = $smarty->fetch($fileName, 'abc');
        return $response->write($content);
    }

    /**
     * 获取 Smarty 实例
     *
     * @param string|null $serverName
     * @return \Smarty
     */
    public function newSmartyInstance($serverName = null)
    {
        if(null === $serverName)
        {
            $server = RequestContext::getServer();
            if($server)
            {
                $serverName = $server->getName();
            }
            else
            {
                throw new \RuntimeException('Not found current server');
            }
        }
        if(!isset(self::$instances[$serverName]))
        {
            $smarty = new \Smarty();
            $smarty->setCacheDir($this->cacheDir ?? Imi::getRuntimePath('smarty/cache'));
            $smarty->setCompileDir($this->compileDir ?? Imi::getRuntimePath('smarty/compile'));
            if(\Smarty::CACHING_OFF !== $this->caching)
            {
                $smarty->setCaching($this->caching);
                $smarty->setCacheLifetime($this->cacheLifetime);
            }
            Event::trigger('IMI.SMARTY.NEW', [
                'smarty'        =>  $smarty,
                'serverName'    =>  $serverName,
            ]);
            self::$instances[$serverName] = $smarty;
        }
        return clone self::$instances[$serverName];
    }

}
