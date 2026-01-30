<?php

namespace PAC\Includes;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Zip_Data class
 *
 * Handles storage and retrieval of ZIP code availability data.
 * Data structure: array<string, array> where each entry contains:
 * - 'status' (bool): Availability status
 * - 'message' (string): Custom message for this ZIP code
 *
 * @package PAC\Includes
 */
class Zip_Data {

    /**
     * Option key for storing ZIP data.
     *
     * @var string
     */
    private static $option_key = 'pac_zip_availability';

    /**
     * Transient key for caching lookup data.
     *
     * @var string
     */
    private static $transient_key = 'pac_zip_lookup';

    /**
     * Get all zip codes with their status and messages.
     *
     * Returns data in normalized format with backward compatibility.
     * Note: This method loads from database. For lookups, use get_lookup_cache() instead.
     *
     * @return array<string, array> Associative array: zip => ['status' => bool, 'message' => string]
     */
    public static function get_all(): array {
        $data = get_option( self::$option_key, [] );

        if ( ! is_array( $data ) ) {
            return [];
        }

        $normalized = [];
        foreach ( $data as $zip => $entry ) {
            $normalized[ $zip ] = [
                'status'  => isset( $entry['status'] ) ? (bool) $entry['status'] : false,
                'message' => isset( $entry['message'] ) ? sanitize_text_field( $entry['message'] ) : '',
            ];
        }

        return $normalized;
    }

    /**
     * Get or build the lookup cache (transient).
     *
     * Caches the full data structure (status + message) for efficient lookups.
     * Only loads from database if transient doesn't exist.
     *
     * @return array<string, array> Cached lookup data: zip => ['status' => bool, 'message' => string]
     */
    private static function get_lookup_cache(): array {
        $lookup = get_transient( self::$transient_key );

        if ( false === $lookup ) {
            $lookup = self::get_all();
            set_transient( self::$transient_key, $lookup, DAY_IN_SECONDS );
        }

        return is_array( $lookup ) ? $lookup : [];
    }

    /**
     * Check if a zip code is available.
     *
     * Uses transient cache for performance.
     *
     * @param string $zip Zip code to check.
     * @return bool True if available, false otherwise.
     */
    public static function is_available( string $zip ): bool {
        $zip  = self::sanitize_zip( $zip );
        $data = self::get_lookup_cache();

        return isset( $data[ $zip ]['status'] ) ? (bool) $data[ $zip ]['status'] : false;
    }

    /**
     * Get custom message for a zip code.
     *
     * Uses transient cache for performance.
     *
     * @param string $zip Zip code to get message for.
     * @return string Custom message, empty string if not set.
     */
    public static function get_message( string $zip ): string {
        $zip  = self::sanitize_zip( $zip );
        $data = self::get_lookup_cache();

        if ( isset( $data[ $zip ]['message'] ) ) {
            return sanitize_text_field( $data[ $zip ]['message'] );
        }

        return '';
    }

    /**
     * Get full data for a zip code (status and message).
     *
     * Uses transient cache for performance.
     *
     * @param string $zip Zip code to get data for.
     * @return array{status: bool, message: string}|null Full data array or null if not found.
     */
    public static function get_zip_data( string $zip ): ?array {
        $zip  = self::sanitize_zip( $zip );
        $data = self::get_lookup_cache();

        if ( isset( $data[ $zip ] ) ) {
            return [
                'status'  => (bool) $data[ $zip ]['status'],
                'message' => sanitize_text_field( $data[ $zip ]['message'] ?? '' ),
            ];
        }

        return null;
    }

    /**
     * Save or update a zip code status and message.
     *
     * @param string $zip     Zip code.
     * @param bool   $status  True = available, False = unavailable.
     * @param string $message Optional custom message. Default empty string.
     * @return bool True on success, false on failure.
     */
    public static function save( string $zip, bool $status, string $message = '' ): bool {
        $zip = self::sanitize_zip( $zip );
        $message = sanitize_textarea_field( $message );

        $data = self::get_all();
        $data[ $zip ] = [
            'status'  => $status,
            'message' => $message,
        ];

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
    public static function delete( string $zip ): bool {
        $zip = self::sanitize_zip( $zip );
        $data = self::get_all();

        if ( isset( $data[ $zip ] ) ) {
            unset( $data[ $zip ] );
            update_option( self::$option_key, $data );
            delete_transient( self::$transient_key );
            return true;
        }

        return false;
    }

    /**
     * Sanitize ZIP code input.
     *
     * Removes spaces, dashes, and converts to uppercase.
     *
     * @param string $zip Raw ZIP code input.
     * @return string Sanitized ZIP code.
     */
    private static function sanitize_zip( string $zip ): string {
        $zip = sanitize_text_field( $zip );
        $zip = preg_replace( '/[\s\-]/', '', $zip );
        return strtoupper( $zip );
    }
}