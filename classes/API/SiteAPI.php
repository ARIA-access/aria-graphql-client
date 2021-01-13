<?php

namespace ARIA\GraphQLClient\API;

use ARIA\GraphQLClient\APIDefinition;
use ARIA\GraphQLClient\Client;
use ARIA\GraphQLClient\CallException;
use ARIA\GraphQLClient\JSONEncodedGQL;

class SiteAPI extends APIDefinition
{


  /**
   * Is the user a member of a site
   * 
   * @param string $site_id UUID of the site
   * @param string $username username of the user 
   */
  public function isMember(string $site_id, string $username): bool
  {

    $query = <<< END
      query {
          isMemberItems(filters: {
          site_id: "$site_id",
          username: "$username"
        })
        {
          is_member
        }
      }
    END;

    $result = $this->getClient()->call($query, Client::METHOD_GET);

    if (!empty($result['data'])) {

      if ($result['data']['isMemberItems'][0]['is_member'] === true) {
        return true;
      }
    }

    return false;
  }

  /**
   * Retrieve usernames who are members of a given site.
   * 
   * When passed a site ID, return the appropriate membership. This function call requires that the 
   * authenticated user be a site admin of the requested site.
   *
   * @param string $site_id
   * @param integer $limit
   * @param integer $offset
   * @return array|null
   */
  public function getMembers( string $site_id, int $limit = 10, int $offset = 0 ) : ? array {

    $query = <<< END
      query {
        getUserGroupSiteMembershipItemFeed(filters: {
          site_id: "$site_id"
        }, first: $limit, fromIndex: $offset)
        {
          nodes {
            site_id
            username
          }
        }
      }
    END;

    $result = $this->getClient()->call($query, Client::METHOD_GET);

    if (!empty($result['data'])) {
      if (!empty($result['data']['getUserGroupSiteMembershipItemFeed']['nodes'])) {
        return $result['data']['getUserGroupSiteMembershipItemFeed']['nodes'];
      }
    }

    return null;
  }

  /**
   * Join the currently authenticated user to a site, if possible
   *
   * @param string $site_id
   * @return boolean
   */
  public function join(string $site_id): bool {

    $mutation = <<< END
      mutation {
        joinSite(input: {
          site_id: "$site_id"
        }) {
            id,
            site_id,
            username
        }
      }
    END;

    $result = $this->getClient()->call($mutation, Client::METHOD_POST);

    if (!empty($result['data'])) {

      if ($result['data']['joinSite']['site_id'] === $site_id) {
        return true;
      }
    }

    return false;
  }

  /**
   * Is the user a site administrator for the site
   * 
   * @param string $site_id UUID of the site
   * @param string $username username of the user 
   */
  public function isAdministrator(string $site_id, string $username): bool
  {

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

      if ($result['data']['isAdministratorItems'][0]['is_admin'] === true) {
        return true;
      }
    }

    return false;
  }

  /**
   * Delete site administrator
   * 
   * @param string $id UUID of the site administrator
   */
  public function deleteSiteAdministrator(string $uuid): bool
  {

    $mutation = <<< END
      mutation {
        deleteSiteAdministrator(input: {
          id: "$uuid",
        }) {
            id,
            site_id,
            username
        }
      }
    END;

    $result = $this->getClient()->call($mutation, Client::METHOD_POST);

    if (!empty($result['data'])) {

      if ($result['data']['deleteSiteAdministrator']['id'] === $uuid) {
        return true;
      }
    }

    return false;
  }

  /**
   * Search site administrators
   * Returns an array of site administrators
   * 
   * @param string $site_id 
   * @param string $username
   */
  public function siteAdministrator(string $site_id = null, string $username = null): array
  {

    $filter = [];
    if (!empty($site_id)) {
      $filter['site_id'] = $site_id;
    }
    if (!empty($username)) {
      $filter['username'] = $username;
    }

    $query = "
      query {
        site_administratorItems(
          filters: " . JSONEncodedGQL::encode($filter) . "
        ) {
            id,
            site_id,
            username
        }
      }
    ";

    $result = $this->getClient()->call($query, Client::METHOD_GET);

    if (!empty($result['data'])) {

      if ($result['data']['site_administratorItems']) {
        return $result['data']['site_administratorItems'];
      }
    }

    return [];
  }

  /**
   * Query the site_domain table to find out details of available sites.
   * 
   * @param array $filter
   * @return array|null
   */
  public function site_domain( array $filter = [] ) : ? array {

    $query = "
    query {
      site_domainItems(
        filters: " . JSONEncodedGQL::encode($filter) . "
      ){
        id
        site_id
        domain
        active
      }
    }
    ";
    
    $result = $this->getClient()->call($query, Client::METHOD_GET);

    if (!empty($result['data'])) {

      if ($result['data']['site_domainItems']) {
        return $result['data']['site_domainItems'];
      }
    }

    return [];
  }
}
