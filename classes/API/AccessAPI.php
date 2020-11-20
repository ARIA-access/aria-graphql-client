<?php

namespace ARIA\GraphQLClient\API;
use ARIA\GraphQLClient\APIDefinition;
use ARIA\GraphQLClient\Client;
use ARIA\GraphQLClient\CallException;

class AccessAPI extends APIDefinition {

  /**
   * Does the currently authenticated user (as defined by your authentication token) have access to view
   * the given user's profile.
   * 
   * This looks at the call and access endpoints and returns true if you are a call/access administrator, or
   * assigned as a reviewer or moderator for a proposal or call application.
   * 
   * @param string $site_id UUID of the site
   * @param string $username UUID of the user
   */
  public function userViewProfile( string $site_id, string $username ) : bool {

    $mutation = <<< END
    mutation {
      userProfileCall(input:{
        site_id: "$site_id",
        username: "$username"
      }) 
      
      userProfileAccess(input: {
        site_id: "$site_id",
        username: "$username"
      })
    }
    END;

    $result = $this->getClient()->call($mutation, Client::METHOD_POST);

    if (!empty($result['data'])) {

        if ( $result['data']['userProfileCall'] === true || $result['data']['userProfileAccess'] === true ) {
            return true;
        }
        
    }

    return false;
  }

}