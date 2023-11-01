<?php

namespace ARIA\GraphQLClient\API\Fields;

trait DataDepositionFields
{
  
  private $bucketFields = '
    id,
    aria_id,
    aria_entity_type,
    owner,
    embargoed_until,
    created,
    updated 
  ';

  private $recordFields = '
    id,
    bucket,
    schema,
    owner,
    created,
    updated
  ';

  private $fieldFields = '
    id,
    record,
    type,
    content,
    options,
    order
  ';
}
