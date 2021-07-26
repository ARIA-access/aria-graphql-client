<?php

namespace ARIA\GraphQLClient\API;

use ARIA\GraphQLClient\API\Fields\SiteFields;
use ARIA\GraphQLClient\APIDefinition;
use ARIA\GraphQLClient\Client;
use ARIA\GraphQLClient\CallException;
use ARIA\GraphQLClient\JSONEncodedGQL;

class SiteAPI extends APIDefinition
{

  use SiteFields;

  const SITE_LOG_LEVEL_DEBUG = 'debug';
  const SITE_LOG_LEVEL_INFO = 'info';
  const SITE_LOG_LEVEL_WARNING = 'warning';
  const SITE_LOG_LEVEL_ERROR = 'error';
  const SITE_LOG_LEVEL_CRIT = 'critical';

  /**
   * Retrieve the logs for a one or more sites that you have access to (are admins for).
   *
   * @param array $filter
   * @param array $order
   * @param integer $limit
   * @param integer $offset
   * @return array
   */
  public function logs(array $filter = [], array $order = [], int $limit = 10, int $offset = 0 ): array
  {

    $query = "
      query {
        site_logItemFeed(
          filters: " . JSONEncodedGQL::encode($filter) . ",
          first: ". $limit. ",
          fromIndex: ". $offset. ",
          sort: " . JSONEncodedGQL::encode($order) . "
        ){
          totalCount,
          pageInfo {
            hasNext,
            endCursor,
            hasNextSlice
          },
          nodes {
            id
            site_id
            message
            level
            context
          }
        }
      }
    ";

    $result = $this->getClient()->call($query, Client::METHOD_GET);

    if (!empty($result['data'])) {

      if ($result['data']['site_logItemFeed']) {
        return $result['data']['site_logItemFeed'];
      }
    }

    return [];

  }

  /**
   * Add a site log entry.
   *
   * @param string $site_id
   * @param string $message
   * @param string $level
   * @param array $context
   * @return boolean
   */
  public function log( string $site_id, string $message, string $level = self::SITE_LOG_LEVEL_INFO, array $context = [] ) : bool {

    $mutation = "
      mutation {
        addSiteLog(input: {
          site_id: \"$site_id\"
          message: \"$message\"
          level: \"$level\"
          context: " . JSONEncodedGQL::encode($context) . "
        }) {
          id
          site_id
          message
          level
          context
        }
      }
      ";

    $result = $this->getClient()->call($mutation, Client::METHOD_POST);

    if (!empty($result['data'])) {

      if (!empty($result['data']['addSiteLog']['id'])) {
        return true;
      }
    }

    return false;
  }

  /**
   * Log a debug log entry
   *
   * @param string $site_id
   * @param string $message
   * @param array $context
   * @return boolean
   */
  public function debug(string $site_id, string $message, array $context = []) : bool {
    return $this->log($site_id, $message, self::SITE_LOG_LEVEL_DEBUG, $context);
  }
  /**
   * Log an info level log entry.
   *
   * @param string $site_id
   * @param string $message
   * @param array $context
   * @return boolean
   */
  public function info(string $site_id, string $message, array $context = []) : bool {
    return $this->log($site_id, $message, self::SITE_LOG_LEVEL_INFO, $context);
  }
  /**
   * Log a warning log level message
   *
   * @param string $site_id
   * @param string $message
   * @param array $context
   * @return boolean
   */
  public function warning(string $site_id, string $message, array $context = []) : bool {
    return $this->log($site_id, $message, self::SITE_LOG_LEVEL_WARNING, $context);
  }
  /**
   * Log an error message in the site log
   *
   * @param string $site_id
   * @param string $message
   * @param array $context
   * @return boolean
   */
  public function error(string $site_id, string $message, array $context = []) : bool {
    return $this->log($site_id, $message, self::SITE_LOG_LEVEL_ERROR, $context);
  }
  /**
   * Log a critical error in the site log
   *
   * @param string $site_id
   * @param string $message
   * @param array $context
   * @return boolean
   */
  public function critical(string $site_id, string $message, array $context = []) : bool {
    return $this->log($site_id, $message, self::SITE_LOG_LEVEL_CRIT, $context);
  }

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
  public function getMembers(string $site_id, int $limit = 10, int $offset = 0): ?array
  {

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
  public function join(string $site_id): bool
  {

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
   * Add site administrator
   * 
   * @param string $uuid UUID of the user
   */
  public function addSiteAdministrator(string $uuid, string $site_id): bool
  {

    $mutation = <<< END
      mutation {
        addSiteAdministrator(input: {
          username: "$uuid",
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

      if ($result['data']['addSiteAdministrator']['username'] === $uuid) {
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
  public function deleteSiteAdministrator(string $uuid, string $site_id): bool
  {

    $mutation = <<< END
      mutation {
        deleteSiteAdministrator(input: {
          username: "$uuid",
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

      if ($result['data']['deleteSiteAdministrator']['username'] === $uuid) {
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
  public function site_domain(array $filter = []): ?array
  {

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

  /**
   * Query the site table to find out details of available sites.
   * 
   * @param array $filter
   * @return array|null
   */
  public function site(array $filter = []): ?array
  {

    $query = "
    query {
      siteItems(
        filters: " . JSONEncodedGQL::encode($filter) . "
      ){
        {$this->siteFields}
      }
    }
    ";

    $result = $this->getClient()->call($query, Client::METHOD_GET);

    if (!empty($result['data'])) {

      if ($result['data']['siteItems']) {
        return $result['data']['siteItems'];
      }
    }

    return [];
  }

  /**
   * Retrieve a list of sites to which I am a member.
   *
   * @param array $filter
   * @return array|null
   */
  public function mySites( array $filter ): ?array 
  {

    $query = "
    query {
      mySitesItems(
        filters: " . JSONEncodedGQL::encode($filter) . "
      ){
        id
        username
        site_id
        siteItems {
          {$this->siteFields}
        }
      }
    }
    ";

    $result = $this->getClient()->call($query, Client::METHOD_GET);

    if (!empty($result['data'])) {

      if ($result['data']['mySitesItems']) {
        return $result['data']['mySitesItems'];
      }
    }

    return [];
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
        usersWithSubmissionOrTeamItems(filters: {
          site_id: "$site_id"
        }, first: $limit, fromIndex: $offset)
        {
          nodes {
            username
            site_id
          }
        }
      }
END;

    $result = $this->getClient()->call($query, Client::METHOD_GET);

    if (!empty($result['data'])) {
      if (!empty($result['data']['usersWithSubmissionOrTeamItemFeed']['nodes'])) {
        return $result['data']['usersWithSubmissionOrTeamItemFeed']['nodes'];
      }
    }

    return null;
  }
}
