<?php

namespace ARIA\GraphQLClient\API\Fields;

trait SiteFields
{
  /**
   * Defining profile fields
   */
  private $siteFields = '
    id
    name
    title
    theme
    terms_url
    privacy_policy_url
    created
    updated
    active
  ';
}
