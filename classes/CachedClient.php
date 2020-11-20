<?php

namespace ARIA\GraphQLClient;

use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Contracts\Cache\ItemInterface;

class CachedClient extends Client {

    private $cache;
    private $expiry;

    public function __construct(AdapterInterface $cache, int $defaultExpiry = 600, string $endpoint = 'https://graphql.aria.services/graphql') {

        $this->cache = $cache;
        $this->expiry = $defaultExpiry;

        parent::__construct($endpoint);

    }

    public function call(string $query, string $mutations = '', string $variables = '', string $method = 'GET') : ? array {

        $expiry = $this->expiry;
        $key = "gql-call-" . sha1( json_encode ([
            $this->getEndpoint(),
            $query,
            $mutations,
            $variables,
            $method
        ]) );

        return $this->cache->get($key, function(ItemInterface $item) use ($expiry, $query, $mutations, $variables, $method) {
        
          $item->expiresAfter( $expiry );

          return parent::call( $query, $mutations, $variables, $method );

        });
    }
}