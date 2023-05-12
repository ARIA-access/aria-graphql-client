<?php

namespace ARIA\GraphQLClient;

use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Contracts\Cache\ItemInterface;

class CachedClient extends Client {

    const EXPIRES_ONE_MINUTE = 60;
    const EXPIRES_FIVE_MINUTES = 300;
    const EXPIRES_TEN_MINUTES = 600;
    const EXPIRES_ONE_HOUR = 3600;
    const EXPIRES_DAY = 86400;

    private $cache;
    private $expiry;

    public function __construct(AdapterInterface $cache, int $defaultExpiry = CachedClient::EXPIRES_TEN_MINUTES, string $endpoint = 'https://graphql.aria.services/graphql') {

        $this->cache = $cache;
        $this->expiry = $defaultExpiry;

        parent::__construct($endpoint);

    }

    protected function getExpiry() : ? int {
        return $this->expiry;
    }

    protected function getKey( array $parameters = [] ) : string {

        $parameters[] = $this->getEndpoint();

        return 'gql-call-' . sha1( json_encode( $parameters ));
        
    }

    public function call(string $query, string $method = Client::METHOD_GET, string $variables = null) : ? array {

        $expiry = $this->getExpiry();
        $key = $this->getKey( [
            $query,
            $variables,
            $method
        ] );

        return $this->cache->get($key, function(ItemInterface $item) use ($expiry, $query, $variables, $method) {
        
          $item->expiresAfter( $expiry );

          return parent::call( $query, $method, $variables );

        });
    }
}
