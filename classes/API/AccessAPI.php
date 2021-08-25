<?php

namespace ARIA\GraphQLClient\API;

use ARIA\GraphQLClient\APIDefinition;
use ARIA\GraphQLClient\Client;
use ARIA\GraphQLClient\CallException;

class AccessAPI extends APIDefinition
{

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
  public function userViewProfile(string $site_id, string $username): bool
  {

    $mutation = <<< END
    query {    
      userProfileCallItems(filters: {
        site_id: "$site_id",
        username: "$username"
      })
      {
        username
        site_id
        userProfileCall
      }

      userProfileAccessItems(filters: {
        site_id: "$site_id",
        username: "$username"
      })
      {
        username
        site_id
        userProfileAccess
      }
    }
END;

    $result = $this->getClient()->call($mutation, Client::METHOD_POST);

    if (!empty($result['data'])) {

      if (
        ($result['data']['userProfileCallItems'][0]['userProfileCall'] !== 0) &&
        ($result['data']['userProfileAccessItems'][0]['userProfileAccess'] !== 0)
      ){
        return true;
      }

    }

    return false;
  }

  /**
   * Does the currently authenticated user (as defined by your authentication token) leave the given site?
   * 
   * This looks at the call and access endpoints and returns true if you are not a member of a proposal or call application team 
   * 
   * @param string $site_id UUID of the site
   * @param string $username UUID of the user
   */
  public function canUserLeaveSite(string $site_id, string $username): bool
  {

    $query = <<< END
    query {    
      canUserLeaveSiteItems(filters: {
        site_id: "$site_id",
        username: "$username"
      })
      {
        username
        site_id
        canUserLeaveSite
      }
    }
END;

    $result = $this->getClient()->call($query, Client::METHOD_POST);

    if (!empty($result['data'])) {

      if ($result['data']['canUserLeaveSiteItems'][0]['canUserLeaveSite'] !== 0) {
        return true;
      }
    }

    return false;
  }

  /**
   * Retrieve usernames of all users who are members of a given site that have a submission or are a member of a team.
   * 
   * The authenticated user making this request must be a site admin of the requested site.
   *
   * @param string $site_id
   * @param integer $limit
   * @param integer $offset
   * @return array|null
   */
  public function getMembersWithSubmissionOrTeam(string $site_id, int $limit = 10, int $offset = 0): ?array
  {

    $query = <<< END
      query {
        usersWithSubmissionOrTeamItemFeed(
          filters: {
            site_id: "$site_id"
          },
          first: $limit,
          fromIndex: $offset
        )
        {
          totalCount,
          pageInfo {
            nextIndex,
            hasNextSlice
          },
          nodes {
            username
          }
        }
      }
END;

    $result = $this->getClient()->call($query, Client::METHOD_GET);

    if (!empty($result['data'])) {
      if (!empty($result['data']['usersWithSubmissionOrTeamItemFeed']['nodes'])) {
        return $result['data']['usersWithSubmissionOrTeamItemFeed'];
      }
    }

    return null;
  }
}
