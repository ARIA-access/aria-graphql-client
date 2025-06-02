<?php

namespace ARIA\GraphQLClient\API\Fields;

trait VisitFields
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
}
