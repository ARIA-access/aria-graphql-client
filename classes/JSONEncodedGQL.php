<?php

namespace ARIA\GraphQLClient;

/**
 * GraphQL compatible JSON tools.
 */
class JSONEncodedGQL {

    /**
     * Is the passed array an _associative_ array (object), or a regular array?
     *
     * @param array $encode
     * @return boolean
     */
    protected static function isAssoc( array $encode ) : bool {
        if (array() === $encode) return false;
        return array_keys($encode) !== range(0, count($encode) - 1);
    }

    /**
     * Encode an array graphQL compatible format (non-quoted keys)
     */
    public static function encode( array $encode, bool $showkeys = true, int $depth = 0 ) : string {

        $return = '';
        
        if ($showkeys) {
            $return .= " { \n";
        } else {
            $return .= " [ \n";
        }

        foreach ( $encode as $key => $value ) {


            for($n = 0; $n < $depth+1; $n++) {
                $return .= "\t";
            }
            if ($showkeys) {
                $return .= " {$key}: ";
            }
                
            if (is_array($value) || is_object($value)) {

                $return .= self::encode( (array) $value, self::isAssoc( (array) $value), $depth+1);

            } else {

                $return .= json_encode($value, JSON_NUMERIC_CHECK) . "\n";

            }
            
        }

        for($n = 0; $n < $depth+1; $n++) {
            $return .= "\t";
        }
        if ($showkeys) {
            $return .= " } \n";
        } else {
            $return .= " ] \n";
        }

        return $return;

    }

}