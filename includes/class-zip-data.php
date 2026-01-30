<?php

namespace PAC\Includes;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Zip_Data
{
    private static $option_key    = 'pac_zip_availability';
    private static $transient_key = 'pac_zip_lookup';

    /**
     * Get all zip codes with their status.
     *
     * @return array<string, bool> Associative array: zip => true|false
     */
    public static function get_all() : array {
        $data = get_option( self::$option_key, [] );
        return is_array( $data ) ? $data : [];
    }

    /**
     * Check if a zip code is available.
     *
     * @param string $zip Zip code to check.
     * @return bool True if available, false otherwise
     */
    public static function is_available( string $zip ) : bool {

        $zip = sanitize_text_field($zip);

        $lookup = get_transient( self::$transient_key );

        if ( $lookup === false ) {
            $lookup = [];

            foreach ( self::get_all() as $code => $status ) {
                $lookup[ $code ] = $status;
            }

            set_transient( self::$transient_key, $lookup, DAY_IN_SECONDS );
        }

        return $lookup[ $zip ] ?? false;
    }

    /**
     * Save or update a zip code status.
     *
     * @param string $zip Zip code.
     * @param bool   $status True = available, False = unavailable.
     * @return bool True on success, false on failure.
     */
    public static function save( string $zip, bool $status ) : bool {

        $zip = sanitize_text_field($zip);

        $data = self::get_all();
        $data[ $zip ] =  $status;

        $success = update_option( self::$option_key, $data );
        delete_transient( self::$transient_key );
        return (bool) $success;
    }

    /**
     * Delete a zip code entry.
     *
     * @param string $zip Zip code to delete.
     * @return bool True if deleted, false if not found.
     */
    public static function delete( string $zip ) : bool {

        $zip = sanitize_text_field($zip);

        $data = self::get_all();

        if ( isset( $data[ $zip ] ) ) {
            unset( $data[ $zip ] );
            update_option( self::$option_key, $data );
            delete_transient( self::$transient_key );
            return true;
        }

        return false;
    }
}