<?php
/**
 * This file is part of the Elephant.io package
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 *
 * @copyright Wisembly
 * @license   http://www.opensource.org/licenses/MIT-License MIT License
 */

namespace ElephantIO\Engine;

use Psr\Log\LoggerInterface;

use ElephantIO\EngineInterface,
    ElephantIO\Exception\UnsupportedActionException;

abstract class AbstractSocketIO implements EngineInterface
{
    const CONNECT      = 0;
    const DISCONNECT   = 1;
    const EVENT        = 2;
    const ACK          = 3;
    const ERROR        = 4;
    const BINARY_EVENT = 5;
    const BINARY_ACK   = 6;

    /** @var string[] Parse url result */
    protected $url;

    /** @var string[] Session information */
    protected $sessions;

    /** @var mixed[] Array of options for the engine */
    protected $options;

    public function __construct($url, array $options = [])
    {
        $this->url     = $this->parseUrl($url);
        $this->options = array_replace($this->getDefaultOptions(), $options);
    }

    /** {@inheritDoc} */
    public function connect()
    {
        throw new UnsupportedActionException($this, 'connect');
    }

    /** {@inheritDoc} */
    public function keepAlive()
    {
        throw new UnsupportedActionException($this, 'keepAlive');
    }

    /** {@inheritDoc} */
    public function close()
    {
        throw new UnsupportedActionException($this, 'close');
    }

    /**
     * Write the message to the socket
     *
     * @param integer $code    type of message (one of EngineInterface constants)
     * @param string  $message Message to send, correctly formatted
     */
    abstract public function write($code, $message = null);

    /** {@inheritDoc} */
    public function emit($event, array $args)
    {
        throw new UnsupportedActionException($this, 'emit');
    }

    /** {@inheritDoc} */
    public function read()
    {
        throw new UnsupportedActionException($this, 'read');
    }

    /** {@inheritDoc} */
    public function getName()
    {
        return 'SocketIO';
    }

    /**
     * Parse an url into parts we may expect
     *
     * @return string[] information on the given URL
     */
    protected function parseUrl($url)
    {
        $server = array_replace(parse_url($url), ['scheme' => 'http',
                                                  'host'   => 'localhost',
                                                  'path'   => 'socket.io']);

        if (!isset($server['port'])) {
            $server['port'] = 'https' === $server['scheme'] ? 443 : 80;
        }

        $server['secured'] = 'https' === $server['scheme'];

        return $server;
    }

    /**
     * Get the defaults options
     *
     * @return array mixed[] Defaults options for this engine
     */
    protected function getDefaultOptions()
    {
        return [['check_ssl' => false,
                 'debug'     => false]];
    }
}

