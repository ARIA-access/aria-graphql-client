<?php

namespace ARIA\GraphQLClient\API\Fields;

trait ProposalFields
{
  /**
   * Defining profile fields
   */
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
}
