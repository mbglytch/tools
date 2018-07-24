<?php

namespace App\Client;

use Cake\Core\InstanceConfigTrait;
use Cake\Http\Client;
use Cake\Http\Client\FormData;
use Cake\Http\Response;
use Cake\Log\LogTrait;
use Psr\Log\LogLevel;


/**
 * Class RestClient
 * @package App\Client
 */
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
     * @var bool Debug mode
     */
    'debug' => false,
    /**
     * @var bool Auth mode
     */
    'auth' => null
  ];

  /**
   * RestClient constructor.
   * @param array $config
   */
  public function __construct($config = [])
  {
    $this->setConfig(array_merge($this->_defaultConfig, $config));
  }

  /**
   * Client GET
   *
   * @param string $endpoint
   * @param string $id
   * @param array|null $options
   * @param array $query
   * @return Response
   */
  public function get($endpoint, $id = null, $options = null, $query = []): Response
  {
    $url = $this->getConfig('url') . $endpoint;
    if (!is_null($id)) {
      $url .= "/$id";
    }
    if (!empty($query)) {
      $url .= '?' . http_build_query($query);
    }

    return $this->connect($url, 'get', $options);
  }

  /**
   * The Http connection method.
   * Handle the Http connection.
   *
   * @param string|null $url The connection url
   * @param string $method The Http method for this connection
   * @param FormData|array|null $options The connection options
   * @return Response The Http Response of the connection
   */
  protected function connect($url, $method, $options = null): Response
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
      $this->log(sprintf(
        "[%s] Connecting to (%s) %s",
        get_class($this), strtoupper($method), $url
      ), LogLevel::DEBUG);
    }

    $http = new Client();
    for ($try = 1; $try <= $this->getConfig('maxTry'); $try++) {
      try {
        $response = $http->$method($url, (string)$data, [
          'headers' => $headers,
          'timeout' => $this->getConfig('timeout'),
          'auth' => $this->getConfig('auth')
        ]);
        usleep($this->getConfig('pause'));
        break;
      } catch (\Exception $e) {
        $this->log(sprintf(
          "[%s] Network error, retrying (try %s/%s) : %s",
          get_class($this), $try, $this->getConfig('maxTry'), $e->getMessage()
        ), LogLevel::WARNING);
        if ($try === $this->getConfig('maxTry')) {
          $message = sprintf(
            "[%s] Network error, max number of try reached (%s), aborting",
            get_class($this), $this->getConfig('maxTry')
          );
          $this->log($message, LogLevel::ERROR);
          throw new \RuntimeException($message);
        }
      } finally {
        if (!$response->isOk()) {
          $this->log(sprintf(
            "[%s] Response from (%s) %s : (%s)",
            get_class($this), strtoupper($method), $url, $response->getStatusCode()
          ), LogLevel::WARNING);
        }

        if ($this->getConfig('debug')) {
          $end = microtime(true);
          $this->log(sprintf(
            "[%s] Response from (%s) %s : (%s) %s in %s sec",
            get_class($this), strtoupper($method), $url, $response->body(), $response->getStatusCode(), ($end - $start)
          ), LogLevel::DEBUG);
        }

        return $response;
      }
    }
  }

  /**
   * Client POST
   *
   * @param string $endpoint
   * @param array|null $options
   * @param array $query
   * @return Response
   */
  public function post($endpoint, $options = null, $query = []): Response
  {
    $url = $this->getConfig('url') . $endpoint;
    if (!empty($query)) {
      $url .= '?' . http_build_query($query);
    }

    return $this->connect($url, 'post', $options);
  }

  /**
   * Client PUT
   *
   * @param string $endpoint
   * @param string $id
   * @param array|null $options
   * @param array $query
   * @return Response
   */
  public function put($endpoint, $id, $options = null, $query = []): Response
  {
    $url = $this->getConfig('url') . $endpoint . '/' . $id;
    if (!empty($query)) {
      $url .= '?' . http_build_query($query);
    }

    return $this->connect($url, 'put', $options);
  }

  /**
   * Client PATCH
   *
   * @param string $endpoint
   * @param string $id
   * @param null $options
   * @param array $query
   * @return Response
   */
  public function patch($endpoint, $id, $options = null, $query = []): Response
  {
    $url = $this->getConfig('url') . $endpoint . '/' . $id;
    if (!empty($query)) {
      $url .= '?' . http_build_query($query);
    }

    return $this->connect($url, 'patch', $options);
  }

  /**
   * Client DELETE
   *
   * @param string $endpoint
   * @param string $id
   * @param array|null $options
   * @param array $query
   * @return Response
   */
  public function delete($endpoint, $id, $options = null, $query = []): Response
  {
    $url = $this->getConfig('url') . $endpoint . '/' . $id;
    if (!empty($query)) {
      $url .= '?' . http_build_query($query);
    }

    return $this->connect($url, 'delete', $options);
  }

}
