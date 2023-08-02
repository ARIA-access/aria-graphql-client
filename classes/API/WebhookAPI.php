<?php

namespace ARIA\GraphQLClient\API;

use ARIA\GraphQLClient\APIDefinition;
use ARIA\GraphQLClient\Client;
use ARIA\GraphQLClient\CallException;
use ARIA\GraphQLClient\JSONEncodedGQL;

class WebhookAPI extends APIDefinition
{

  private $subscribersFields = '
    id
    site_id
    label
    hook
    context
    url
    created
    updated
  ';

  private $webhookFields = '
    id
    username
    event
    context
    payload
    created
    processed
  ';

  /**
   * Retrieve the subscriber endpoints for a hook
   *
   * @param array $filter
   * @param array $order
   * @param integer $limit
   * @param integer $offset
   * @return array
   */
  public function subscribers(array $filter, array $order = [], int $limit = 10, int $offset = 0): array
  {
    $query = "
    query {
      hook_subscriberItemFeed(
        filters: " . JSONEncodedGQL::encode($filter) . ",
        first: " . $limit . ",
        fromIndex: " . $offset . ",
        sort: " . JSONEncodedGQL::encode($order) . "
      ){
        totalCount,
        pageInfo {
          hasNext,
          endCursor,
          hasNextSlice
        },
        nodes {
          {$this->subscribersFields}
        }
      }
    }
  ";

    $result = $this->getClient()->call($query, Client::METHOD_GET);

    if (!empty($result['data'])) {

      if ($result['data']['hook_subscriberItemFeed']) {
        return $result['data']['hook_subscriberItemFeed'];
      }
    }

    return [];
  }




  /**
   * Add a hook subscriber.
   *
   * @param string $site_id
   * @param string $label
   * @param string $hook
   * @param array $context
   * @param string $url
   * @return boolean
   */
  public function addHookSubscriber(
    string $site_id,
    string $label,
    string $hook,
    array $context,
    string $url
  ): ?array {
    $mutation = "
      mutation {
        addHookSubscriber(input: {
          site_id: \"$site_id\",
          label: \"$label\",
          hook: \"$hook\",
          url: \"$url\",
          context: " . JSONEncodedGQL::encode($context) . "
        }){
          {$this->subscribersFields}
        }
      }
    ";

    $result = $this->getClient()->call($mutation, Client::METHOD_POST);

    if (!empty($result['data'])) {

      if (isset($result['data']['addHookSubscriber']['id'])) {
        return $result['data']['addHookSubscriber'];
      }
    }

    return null;
  }


  /**
   * Remove an existing hook
   *
   * @param string $id
   * @return boolean
   */
  public function removeHookSubscriber(string $id): bool
  {
    $mutation = "
      mutation {
        removeHookSubscriber(input: {
          id: \"$id\"
        }){
          id
        }
      }
    ";

    $result = $this->getClient()->call($mutation, Client::METHOD_POST);

    if (!empty($result['data'])) {

      if (isset($result['data']['removeHookSubscriber']['id'])) {
        return true;
      }
    }

    return false;
  }


  /**
   * Dispatch a webhook to the queue.
   *
   * @param string $event The event, e.g. `visit.create`
   * @param array $context The context (i.e. the "address on the envelope") a key value pair
   * @param array $payload The payload
   * @return array|null
   */
  public function dispatchWebhook(
    string $event,
    array $context,
    array $payload
  ): ?array {
    $mutation = "
    mutation {
      dispatchWebhook(input: {
        event: \"$event\",
        context: " . JSONEncodedGQL::encode($context) . ",
        payload: " . JSONEncodedGQL::encode($payload) . "
      }){
        {$this->webhookFields}
      }
    }
  ";

    $result = $this->getClient()->call($mutation, Client::METHOD_POST);

    if (!empty($result['data'])) {
      
      if (isset($result['data']['dispatchWebhook']['id'])) {
        return $result['data']['dispatchWebhook'];
      }
    }

    return null;
  }
}
