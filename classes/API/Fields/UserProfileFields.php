<?php

namespace ARIA\GraphQLClient\API\Fields;

trait UserProfileFields
{
  /**
   * Defining profile fields
   */
  private $userProfileFields = '
    username,
    first_name, 
    last_name,
    email,
    perm_group_id,
    avatar,
    gender,
    nationality,
    country_of_residence,
    organization_id,
    publication,
    bio,
    specialization,
    career_stage,
    orcid,
    orcid_settings,
    aria_uid,
    timezone,
    alt_emails
  ';
}
