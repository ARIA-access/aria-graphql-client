<?php

namespace ARIA\GraphQLClient\API\Fields;

trait AccessFields
{
  /**
   * Defining profile fields
   */
  private $visitFields = '
    id,
    plid,
    status,
    order,
    confirmed,
    completed,
    cancelled,
    detail,
    tech_eval_positive,
    suspension_count, 
    cid,
    access_id,
    proposal_id,
    call_id
  ';

  private $proposalFields = '
    id,
    status,
    submitted,
    confirmed,
    approved,
    completed,
    title,
    username,
    moderator, 
    action
  ';

  private $facilityFields = '
    id,
    name,
    country,
    lat,
    lng,
    id
  ';

  private $accessFields = '
    id,
    title,
    site_id,
    uri,
    image,
    description,
    techreview,
    open,
    close,
    access_type
  ';

  private $machineFields = '
    id,
    name,
    description,
    hidden,
    remote,
    physical,
    bookable,
    procedure,
    centre,
    site_id,
    facility_id
  ';
}
