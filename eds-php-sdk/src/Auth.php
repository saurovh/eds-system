<?php

namespace Saurovh\EdsPhpSdk;

use Saurovh\EdsPhpSdk\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use Saurovh\EdsPhpSdk\Http\Response as EdsResponse;
use Exception;
use GuzzleHttp\RequestOptions;

class Auth
{
    use HasApiClient, HasLoggerTrait;

    /**
     * @var int
     */
    private $appId;
    /**
     * @var string
     */
    private $authCode;
    /**
     * @var string
     */
    private $secret;

    /**
     * @var Client
     */
    private $httpClient;

    /**
     * @var string
     */
    protected $domain;

    /**
     * @var bool
     */
    private $sandboxMode;

    public function __construct(int $appId, string $secret, string $authCode = '', bool $sandboxMode = false)
    {
        $this->appId       = $appId;
        $this->secret      = $secret;
        $this->authCode    = $authCode;
        $this->sandboxMode = $sandboxMode;

        $this->domain = ApiConfig::API_URL;

        $this->httpClient = new Client;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    protected function getUri(string $path): string
    {
        return sprintf('%s/v%s/%s/', $this->domain, ApiConfig::APIVersion, $path);
    }

    /**
     * @param string $method
     * @param string $path
     * @param array  $params
     *
     * @return EdsResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function execute(string $path, array $params, string $method = Request::METHOD_POST)
    {
        try {
            $options = [];
            if ($method === Request::METHOD_GET) {
                $options[RequestOptions::QUERY] = $params;
            } else if (!empty($params)) {
                $options[RequestOptions::JSON] = $params;
            }
            $debugData = 'POST' . ' ' . $this->getUri($path) . PHP_EOL . var_export($params, true);
            $this->getLogger()->debug($debugData);
            $response = $this->httpClient->request($method, $this->getUri($path), $options);
        } catch (RequestException $exception) {
            $this->getLogger()->error($exception);
            $response = $exception->hasResponse() ? $exception->getResponse() : new Response(400, [], null, $exception->getMessage());
        } catch (Exception $exception) {
            $this->getLogger()->error($exception);
            $response = new Response(400, [], null, $exception->getMessage());
        }

        return EdsResponse::create($response);
    }
}