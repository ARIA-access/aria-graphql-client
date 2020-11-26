<?php

namespace ARIA\GraphQLClient\API;
use ARIA\GraphQLClient\APIDefinition;
use ARIA\GraphQLClient\Client;
use ARIA\GraphQLClient\CallException;

class SiteAPI extends APIDefinition {

  /**
   * Is the user a site administrator for the site
   * 
   * @param string $site_id UUID of the site
   * @param string $username username of the user 
   */
  public function isAdministrator( string $site_id , string $username) : bool {

    $query = <<< END
      query {
          isAdministratorItems(filters: {
          site_id: "$site_id",
          username: "$username"
        })
        {
          is_admin
        }
      }
    END;

    $result = $this->getClient()->call($query, Client::METHOD_GET);

    if (!empty($result['data'])) {

        if ( $result['data']['isAdministratorItems'][0]['is_admin'] === true ) {
            return true;
        }
        
    }

    return false;
  }

}