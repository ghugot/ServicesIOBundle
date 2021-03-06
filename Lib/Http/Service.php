<?php
/**
 * Http : service accessor
 *
 * PHP Version 5
 *
 * @category Http
 * @package  Redgem\ServicesIOBundle
 * @author   Guillaume HUGOT <guillaume.hugot@gmail.com>
 * @license  MIT
 * @link     http://github.com/ghugot/ServicesIO
 */

namespace Redgem\ServicesIOBundle\Lib\Http;

use Psr\Log\LoggerInterface;

/**
 * the Service class furnish helpers to build
 * the differents objects needed by ServicesIO Http requesting
 *
 * @category Http
 * @package  Redgem\ServicesIOBundle
 * @author   Guillaume HUGOT <guillaume.hugot@gmail.com>
 * @license  MIT
 * @link     http://github.com/ghugot/ServicesIO
 */
class Service
{
	/**
	 *
	 * @var LoggerInterface
	 */
	private $_monolog;

	/**
	 * @var array
	 */
	private $_config;

    /**
     * @var string
     */
    private $_directory;

	/**
	 * @var array
	 */
	private $_httpLogger;

    /**
     * the constructor
     */
    public function __construct(LoggerInterface $monolog, $config, string $directory, $httpLogger = null)
    {
		$this->_monolog = $monolog;
		$this->_config = $config;
        $this->_directory = $directory;
		$this->_httpLogger = $httpLogger;
    }

    /**
     * Create a new Request
     *
     * @return Request
     */
    public function createRequest()
    {
        $request = new Request();

        if (isset($this->_config['user_agents']) && sizeof($this->_config['user_agents']) > 0) {
        	$uas = $this->_config['user_agents'];
        	$request->setUserAgent($uas[mt_rand(0, sizeof($uas) - 1)]['user_agent']);
        }

        if (isset($this->_config['cookies_jars']) && sizeof($this->_config['cookies_jars']) > 0) {
        	$cjs = $this->_config['cookies_jars'];
        	$request->setCookiesJar($cjs[mt_rand(0, sizeof($cjs) - 1)]['cookies_jar']);
        }

        if (isset($this->_config['interfaces']) && sizeof($this->_config['interfaces']) > 0) {
        	$ifs = $this->_config['interfaces'];
        	$request->setInterface($ifs[mt_rand(0, sizeof($ifs) - 1)]['interface']); 
        }

        return $request;
    }

    /**
     * Create a new Pool
     *
     * @return Pool
     */
    public function createPool()
    {
        $pool = null;
        if (isset($this->_config['mock']['activated']) && $this->_config['mock']['activated']) {
            $pool = new MockedPool(
                $this->_monolog,
                $this->_config['mock']['responses'],
                $this->_directory
            );
        } else {
            $pool = new Pool(
                $this->_monolog
            );
        }

        if ($this->_httpLogger) {
        	$this->_httpLogger
        		->addPool($pool);
        }

        return $pool;
    }
}
