<?php

namespace ARIA\GraphQLClient;

use Firebase\JWT\JWT;

/**
 * AccessTokenAwareCachedClient
 * 
 * An extension to the cached client that ensures that caches are contained to the authenticated user.
 */
class AccessTokenAwareCachedClient extends CachedClient
{

    protected function getKey(array $parameters = []): string
    {
        // Are we logged in?
        if ($token = $this->bearerToken()) {

            list($header, $payload, $signature) = explode(".", $token);

            $header = json_decode(JWT::urlsafeB64Decode($header), true);
            $payload = json_decode(JWT::urlsafeB64Decode($payload), true);

            if (empty($header) || empty($payload)) {
                throw new \RuntimeException('Sorry, token provided is not a valid JWT');
            }

            // Extract the token subject
            $username = $payload['sub'];

            $parameters[] = $username;
        }

        return parent::getKey($parameters);
    }

    /** 
     * Retrieve a bearer authentication token if present
     * @return string|null
     */
    protected function bearerToken(): ?string
    {

        $headers = null;
        $serverheaders = $this->headers();

        if (isset($serverheaders['Authorization']))
            $headers = trim($serverheaders["Authorization"]);
        else if (isset($serverheaders['HTTP_AUTHORIZATION']))
            $headers = trim($serverheaders["HTTP_AUTHORIZATION"]);

        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/i', $headers, $matches)) {
                return trim($matches[1], '\'"');
            }
        }

        return null;
    }

    /**
     * Get client request headers.
     * Retrieve all client request headers, note that this should be used instead of getallheaders(), which isn't
     * always available. 
     * @param $header Optional header to return.
     * @return array|string The specific header interested in, or the whole header array of not present
     */
    public function headers(?string $header = null)
    {

        $headers = [];

        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 14) == 'REDIRECT_HTTP_') {
                $name = substr($name, 9);
            }

            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }

        if (!empty($header)) {
            return $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('-', ' ', $header))))];
        }

        return $headers;
    }
}
