<?php

namespace ARIA\GraphQLClient\API;
use ARIA\GraphQLClient\APIDefinition;
use ARIA\GraphQLClient\Client;
use ARIA\GraphQLClient\CallException;

class UserAPI extends APIDefinition {

  /**
   * Search users
   * Returns an array of users
   * 
   * @param array $filter array of variables to filter on
   */
  public function user( array $filter ) : array {
    
    $query = <<< END
    query {
      userItems(
        filters: {
          site_id: "$filter[site_id]",
          username: "$filter[username]",
          first_name: "$filter[first_name]",
          last_name: "$filter[last_name]",
          email: "$filter[email]"
        }
      ){
        username,
        first_name, 
        last_name,
        email,
        perm_group_id,
        avatar,
        gender:,
        nationality,
        country_of_residence,
        organization_id,
        publication,
        bio,
        specialization,
        career_stage,
        orcid,
        orcid_settings
      }
    }
    END;

    $result = $this->getClient()->call($query, Client::METHOD_GET);
    
        if (!empty($result['data'])) {

          if ( $result['data']['userItems'] ) {
              return $result['data']['userItems'];
          }
        
        }

    return [];
  }

}