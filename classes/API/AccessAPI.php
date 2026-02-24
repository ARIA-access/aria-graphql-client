<?php

namespace ARIA\GraphQLClient\API;

use ARIA\GraphQLClient\API\Fields\VisitFields;
use ARIA\GraphQLClient\APIDefinition;
use ARIA\GraphQLClient\Client;
use ARIA\GraphQLClient\CallException;

class AccessAPI extends APIDefinition
{

  use VisitFields;

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
   * Retrieve usernames of all users who are members of a given site that have a submission or are a member of a team or who are reviewers or moderators.
   * 
   *
   * @param string $site_id
   * @param integer $limit
   * @param integer $offset
   * @return array|null
   */
  public function getVisibleMembersDataForSite(string $site_id, int $limit = 10, int $offset = 0): ?array
  {

    $query = <<< END
      query {
        getVisibleMembersDataForSiteItemFeed(
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
      if (!empty($result['data']['getVisibleMembersDataForSiteItemFeed']['nodes'])) {
        return $result['data']['getVisibleMembersDataForSiteItemFeed'];
      }
    }

    return null;
  }

  /**
   * Retrieve a visit by its visit ID
   */
  public function visit(int $visit_id) {

    $query = <<< END
      query {
        visitItems(
          filters: {
            id: $visit_id
          }
        ) {
          $this->visitFields
        }
      }
END;

    $result = $this->getClient()->call($query, Client::METHOD_GET);

    if (!empty($result['data'])) {

      if ($result['data']['visitItems']) {
        return $result['data']['visitItems'];
      }
    }

    return [];
  }

  /**
   * Retrieve proposals
   *
   *
   * @param array<string, mixed> $filters
   * @return array
   */
  public function proposalItems(array $filters = [])
  {
      // Allowed filter keys are the fields in $proposalFields
      $allowedFields = array_map(
          'trim',
          explode(',', str_replace(["\n", "\r"], '', $this->proposalFields))
      );

      $graphqlFilters = [];

      foreach ($filters as $field => $value) {

          // Skip invalid fields
          if (!in_array($field, $allowedFields, true)) {
              continue;
          }

          // Skip empty values
          if ($value === null || $value === '') {
              continue;
          }

          // Format values
          // Requires numbers without quotes and strings with quotes
          if (is_int($value) || is_float($value)) { // If numeric (e.g. proposal_id: 28)
              $graphqlFilters[] = "$field: $value";
          } else {
              $escaped = addslashes($value);  // Escape quotes inside strings to prevent breaking the query

              $graphqlFilters[] = "$field: \"$escaped\"";  // Wrap string values in quotes (e.g. status: "SUBMITTED")

          }
      }

      // If no valid filters, return empty array
      if (empty($graphqlFilters)) {
          return [];
      }

      $filtersString = implode("\n", $graphqlFilters);

      $query = <<<END
      query {
          proposalItems(
              filters: {
                  $filtersString
              }
          ) {
              $this->proposalFields
          }
      }
  END;

      $result = $this->getClient()->call($query, Client::METHOD_GET);

      return $result['data']['proposalItems'] ?? [];
  }
  
  /**
   * Fetch visit items using dynamic GraphQL filters.
   *
   * The method accepts an associative array of filters where:
   * - The key must match a field defined in $visitFields
   * - The value is the value to filter by
   *
   * Only valid, non-empty filters are included in the GraphQL query.
   * If no valid filters are provided, an empty array is returned.
   *
   * Example:
   *   visitItems(['proposal_id' => 28])
   *   visitItems(['id' => 21, 'status' => 'AWAITING_EVALUATION'])
   *
   * @param array<string, mixed> $filters
   *   Associative array of visit field => value pairs
   *
   * @return array
   *   List of visit items matching the provided filters
   */
  public function visitItems(array $filters = [])
  {
      // Allowed filter keys are the fields in $visitFields
      $allowedFields = array_map('trim', explode(',', str_replace(["\n", "\r"], '', $this->visitFields)));

      $graphqlFilters = [];

      // Loop through user filters
      foreach ($filters as $field => $value) {
          // Skip if field is not allowed
          if (!in_array($field, $allowedFields, true)) {
              continue;
          }

          // Skip empty values
          if ($value === null || $value === '') {
              continue;
          }

          // Decide how to format the value
          if (is_int($value) || is_float($value)) {
              $graphqlFilters[] = "$field: $value";
          } else {
              // Escape quotes for GraphQL strings
              $escaped = addslashes($value);
              $graphqlFilters[] = "$field: \"$escaped\"";
          }
      }

      // Return empty array if no valid filters
      if (empty($graphqlFilters)) {
          return [];
      }

      // Build GraphQL query
      $filtersString = implode("\n", $graphqlFilters);

      $query = <<<END
      query {
          visitItems(
              filters: {
                  $filtersString
              }
          ) {
              $this->visitFields
          }
      }
  END;

      $result = $this->getClient()->call($query, Client::METHOD_GET);

      return $result['data']['visitItems'] ?? [];
  }

}
