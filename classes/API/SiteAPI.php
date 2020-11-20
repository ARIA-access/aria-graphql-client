<?php

namespace ARIA\GraphQLClient\API;
use ARIA\GraphQLClient\APIDefinition;
use ARIA\GraphQLClient\Client;
use ARIA\GraphQLClient\CallException;

class SiteAPI extends APIDefinition {

  /**
   * Is the currently authenticated user a site administrator for the site.
   * 
   * @param string $site_id UUID of the site
   */
  public function isAdministrator( string $site_id ) : bool {

    $mutation = <<< END
    mutation {
      isAdministrator(input:{
        site_id: "$site_id"
      })
    }
    END;

    $result = $this->getClient()->call($mutation, Client::METHOD_POST);

    if (!empty($result['data'])) {

        if ( $result['data']['isAdministrator'] === true ) {
            return true;
        }
        
    }

    return false;
  }

}