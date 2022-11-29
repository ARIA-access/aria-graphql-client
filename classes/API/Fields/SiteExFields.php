<?php

namespace ARIA\GraphQLClient\API\Fields;

trait SiteExFields
{
  /**
   * Defining profile fields
   */
  private $siteExFields = '
    id
    name
    title
    theme
    terms_url
    privacy_policy_url
    created
    updated
    active
    administrators
  ';
}
