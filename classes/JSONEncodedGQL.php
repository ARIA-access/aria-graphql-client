<?php

namespace ARIA\GraphQLClient;

/**
 * GraphQL compatible JSON tools.
 */
class JSONEncodedGQL {

    /**
     * Encode an array graphQL compatible format (non-quoted keys)
     */
    public static function encode( array $encode ) : string {

        $return = '';

        $return .= " { \n";

        foreach ( $encode as $key => $value ) {

            $return .= " \t {$key}: ";
                
            if (is_array($value) || is_object($value)) {

                $return .= self::encode( (array) $value);

            } else {

                $return .= json_encode($value, JSON_NUMERIC_CHECK) . "\n";

            }
            
        }

        $return .= " } \n";

        return $return;

    }

}