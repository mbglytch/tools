<?php

namespace App\Client;

use Cake\Core\InstanceConfigTrait;
use Cake\Http\Client;
use Cake\Http\Client\FormData;
use Cake\Log\LogTrait;
use Psr\Log\LogLevel;
use Exception;
use InvalidArgumentException;


class RestClient
{
    use InstanceConfigTrait;
    use LogTrait;

    /**
     * Default configuration.
     *
     * These are merged with user-provided configuration when the behavior is used.
     *
     * @var array
     */
    protected $_defaultConfig = [
        /**
         * @var int Maximum number of connection try if fail/timeout
         */
      'maxTry' => 3,
        /**
         * @var int Connection timeout, seconds
         */
      'timeout' => 10,
        /**
         * @var int Sleep after each connection, milliseconds
         */
      'pause' => 150,
        /**
         * @var string Api url
         */
      'url' => null,
        /**
         * @var string Api key
         */
      'key' => null,
        /**
         * @var bool Debug mode
         */
      'debug' => false,
        /**
         * @var bool Auth mode
         */
      'auth' => null,
        /**
         * @var string Current endpoint
         */
      'endpoint' => null
    ];

    /**
     * RestClient constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->setConfig(array_merge($this->_defaultConfig, $config));
    }

    public function get($endpoint, $id = null, $options = null, $query_string = null)
    {
        $url = $this->getConfig('url') . $endpoint . $query_string;
        if (!is_null($id)) {
            $url .= "/$id";
        }

        return $this->_connect($url, 'get', $options);
    }

    public function post($endpoint, $options = null, $query_string = null)
    {
        $url = $this->getConfig('url') . $endpoint . $query_string;

        return $this->_connect($url, 'post', $options);
    }

    public function put($endpoint, $id, $options = null, $query_string = null)
    {
        $url = $this->getConfig('url') . $endpoint . '/' . $id . $query_string;

        return $this->_connect($url, 'put', $options);
    }

    public function patch($endpoint, $id, $options = null, $query_string = null)
    {
        $url = $this->getConfig('url') . $endpoint . '/' . $id . $query_string;

        return $this->_connect($url, 'patch', $options);
    }

    public function delete($endpoint, $id, $options = null, $query_string = null)
    {
        $url = $this->getConfig('url') . $endpoint . '/' . $id . $query_string;

        return $this->_connect($url, 'delete', $options);
    }

    /**
     * The http connection method.
     * Handle the http connection.
     *
     * @param string|null $url The connection url
     * @param string $method The http method for this connection
     * @param FormData|array|null $options The connection options
     * @return bool|string False if response not ok, response->getBody() otherwise
     */
    protected function _connect($url, $method, $options = null)
    {
        $headers = [];
        if (is_array($options) && !empty($options)) {
            $data = new FormData();
            $data->addMany($options);
            $headers['Content-Type'] = $data->contentType();
        } else {
            $data = $options;
        }

        if ($this->getConfig('debug')) {
            $start = microtime(true);
            $this->log(sprintf("[%s] Connecting to (%s) %s",
              get_class($this),
              strtoupper($method),
              $url
            ), LogLevel::INFO);
        }

        $http = new Client();
        for ($try = 1; $try <= $this->getConfig('maxTry'); $try++) {
            try {
                $response = $http->$method($url, (string) $data, [
                  'headers' => $headers,
                  'timeout' => $this->getConfig('timeout'),
                  'auth' => $this->getConfig('auth')
                ]);
                usleep($this->getConfig('pause'));
                break;
            } catch (Exception $e) {
                $this->log(sprintf("[%s] Network error, retrying (try %s/%s) : %s",
                  get_class($this),
                  $try,
                  $this->getConfig('maxTry'),
                  $e->getMessage()
                ), LogLevel::WARNING);
                if ($try === $this->getConfig('maxTry')) {
                    $this->log(sprintf("[%s] Network error, max number of try reached (%s), aborting",
                      get_class($this),
                      $this->getConfig('maxTry')
                    ), LogLevel::ERROR);
                    return false;
                }
            }
        }
        $statusCode = $response->getStatusCode();
        $body = $response->body();
        if ($statusCode < 200 || $statusCode >= 300) {
            $this->log(sprintf("[%s] Response from (%s) %s : (%s)",
              get_class($this),
              strtoupper($method),
              $url,
              $statusCode
            ), LogLevel::WARNING);
            return false;
        }

        if ($this->getConfig('debug')) {
            $end = microtime(true);
            $this->log(sprintf("[%s] Response from (%s) %s : (%s) %s in %s sec",
              get_class($this),
              strtoupper($method),
              $url,
              $body,
              $statusCode,
              ($end - $start)
            ), LogLevel::INFO);
        }

        return $body ? $body : false;
    }

}
