<?php

namespace ARIA\GraphQLClient;

class DateTime {

    /**
     * Create ISO date from timestamp
     *
     * @param integer $unix_ts
     * @return string
     */
    public static function iso8601Date(int $unix_ts) : string {

        return date('Y-m-d\TH:i:s\Z', $unix_ts);

    }
}