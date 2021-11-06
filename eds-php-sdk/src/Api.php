<?php

namespace Saurovh\EdsPhpSdk;

use Saurovh\EdsPhpSdk\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;
use Exception;
use Saurovh\EdsPhpSdk\Http\Response as EdsResponse;

class Api
{
    use HasLoggerTrait;

    /**
     * @var Api
     */
    protected static $instance;

    private $apiKey;

    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * @param Client $httpClient
     * @param string $apiKey A API key
     */
    public function __construct(
        Client $httpClient,
        string $apiKey)
    {
        $this->httpClient = $httpClient;
        $this->apiKey     = $apiKey;
    }

    public static function init(string $apiKey)
    {
        $api = new static(new Client(), $apiKey);
        static::setInstance($api);

        return $api;
    }

    /**
     * @return Api|null
     */
    public static function instance()
    {
        return static::$instance;
    }

    /**
     * @param Api $instance
     */
    public static function setInstance(Api $instance)
    {
        static::$instance = $instance;
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @return Client
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    protected function getUri(string $path): string
    {
        return sprintf('%s/v%s/%s', ApiConfig::API_URL, ApiConfig::APIVersion, $path);
    }

    /**
     * Make graph api calls
     *
     * @param string $path       Ads API endpoint
     * @param array  $params     Assoc of request parameters
     * @param array  $fileParams array format should be like
     *                           ```[
     *                           'name'     => 'baz',
     *                           'contents' => fopen('/path/to/file', 'r')
     *                           ],
     *                           [
     *                           'name'     => 'qux',
     *                           'contents' => fopen('/path/to/file', 'r'),
     *                           'filename' => 'custom_filename.txt'
     *                           ]```
     * @param string $method     request method
     *
     * @return Http\ResponseInterface API responses
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function call(
        string $path,
        array $params = [],
        array $fileParams = [],
        string $method = Request::METHOD_GET
    )
    {
        try {
            $options     = [];
            $queryParams = [];
            if (!empty($fileParams)) {
                /**
                 * https://docs.guzzlephp.org/en/latest/request-options.html#multipart
                 */
                foreach ($params as $key => $value) {
                    $fileParams[] = [
                        'name'     => $key,
                        'contents' => $value
                    ];
                }
                $options[RequestOptions::MULTIPART] = $fileParams;
            } else if ($method === Request::METHOD_GET) {
                $queryParams = $params;
            } else if (!empty($params)) {
                $options[RequestOptions::JSON] = $params;
            }
            if (isset($this->apiKey)) {
                $queryParams = array_merge([
                    'api_key' => $this->apiKey
                ], $queryParams);
            }
            $options[RequestOptions::QUERY] = $queryParams;
            $headers                        = [];
            if (!empty($headers)) {
                $options[RequestOptions::HEADERS] = $headers;
            }
            $debugData = $method . ' ' . $this->getUri($path) . PHP_EOL . var_export($options, true);
            $this->getLogger()->debug($debugData);
            $response = $this->getHttpClient()->request($method, $this->getUri($path), $options);
        } catch (RequestException $exception) {
            $this->getLogger()->error($exception);
            $response = $exception->hasResponse() ? $exception->getResponse() : new Response(400, [], null, '1.1', $exception->getMessage());
        } catch (Exception $exception) {
            $this->getLogger()->error($exception);
            $response = new Response(400, [], null, '1.1', $exception->getMessage());
        }
        $debugData = 'CODE: ' . $response->getStatusCode() . PHP_EOL . 'Body: ' . $response->getBody()->getContents()
            . PHP_EOL . 'Reason: ' . var_export($response->getReasonPhrase(), true);
        $this->getLogger()->debug($debugData);

        return EdsResponse::create($response, $exception ?? null);
    }

    /**
     * @param       $path
     * @param array $params
     *
     * @return Http\ResponseInterface
     */
    public function get($path, array $params = [])
    {
        return $this->call($path, $params);
    }

    /**
     * @param       $path
     * @param array $params
     * @param array $fileParams
     *
     * @return Http\ResponseInterface
     */
    public function post($path, array $params = [], array $fileParams = [])
    {
        return $this->call($path, $params, $fileParams, Request::METHOD_POST);
    }

    /**
     * @param       $path
     * @param array $params
     * @param array $fileParams
     *
     * @return Http\ResponseInterface
     */
    public function put($path, array $params = [], array $fileParams = [])
    {
        return $this->call($path, $params, $fileParams, Request::METHOD_PUT);
    }

    /**
     * @param       $path
     * @param array $params
     *
     * @return Http\ResponseInterface
     */
    public function delete($path, array $params = [])
    {
        return $this->call($path, $params, [],Request::METHOD_DELETE);
    }

    /**
     * @param Client $httpClient
     */
    public function setHttpClient(Client $httpClient): void
    {
        $this->httpClient = $httpClient;
    }

}