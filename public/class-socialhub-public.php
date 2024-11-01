<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://socialhub.io/
 * @since      1.0.0
 */

/**
 * Require JWT library.
 */
use \Firebase\JWT\JWT;

/**
 * The public-facing functionality of the plugin.
 */
class SocialHub_Public {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     *
     * @var string The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     *
     * @var string The current version of this plugin.
     */
    private $version;

    /**
     * The namespace to add to the api calls.
     *
     * @var string The namespace to add to the api call
     */
    private $namespace;

    /**
     * Store errors to display if the JWT is invalid.
     *
     * @var WP_Error
     */
    private $jwt_error = null;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     *
     * @param string $plugin_name The name of the plugin.
     * @param string $version     The version of this plugin.
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->namespace = $this->plugin_name . '/v' . intval($this->version);
    }

    /**
     * This is our Middleware trying to authenticate the user according to the
     * token send.
     *
     * @param (int|bool) $user Logged User ID
     *
     * @return (int|bool)
     */
    public function determine_current_user($user) {
        // Only validate token on REST API routes.
        $rest_api_slug = rest_get_url_prefix();
        $valid_api_uri = strpos($_SERVER['REQUEST_URI'], $rest_api_slug);
        if(!$valid_api_uri){
            return $user;
        }

        if($user) return $user;

        $token = $this->validate_token();

        if (is_wp_error($token)) {
            if ($token->get_error_code() != 'socialhub_no_auth_token') {
                // Store errors for rest_pre_dispatch.
                $this->jwt_error = $token;
                return $user;
            } else {
                return $user;
            }
        }

        // Return User ID of verified token.
        return $token->user->id;
    }

    /**
     * Validate the requests SocialHub JWT.
     *
     * @return WP_Error | Object | Array
     */
    public function validate_token() {
        // Look for token in Authorization header.
        $auth = isset($_SERVER['HTTP_AUTHORIZATION']) ?  $_SERVER['HTTP_AUTHORIZATION'] : false;
        // Look for token in Redirected Authorization header.
        if (!$auth) {
            $auth = isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']) ?  $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] : false;
        }

        // Look for token in URI query parameter.
        if (!$auth) {
            $token = isset($_GET['access_token']) ?  $_GET['access_token'] : false;
            // Error if token could not be found.
            if (!$token) {
              return new WP_Error(
                  'socialhub_no_auth_token',
                  __('Authorization token not found.', 'socialhub'),
                  array(
                      'status' => 403,
                  )
              );
            }
        }
        else {
            // Extract token from Authorization header value.
            list($token) = sscanf($auth, 'Bearer %s');
            if (!$token) {
                return new WP_Error(
                    'socialhub_bad_auth_header',
                    __('Authorization header malformed.', 'socialhub'),
                    array(
                        'status' => 403,
                    )
                );
            }
        }

        // Attempt decoding the token.
        $secret_key = AUTH_KEY;
        try {
            // This will verify whether the secret key the token was generated
            // with matches this sites key and if it does it will decode and
            // return the tokens payload.
            $token = JWT::decode($token, $secret_key, array('HS256'));
            // Verify that the issuer matches with our plugin and version.
            if ($token->iss != SOCIALHUB_ISSUER) {
                return new WP_Error(
                    'socialhub_bad_iss',
                    __('The iss do not match with this server', 'socialhub'),
                    array(
                        'status' => 403,
                    )
                );
            }
            // Ensure the token payload carries a User ID.
            if (!isset($token->user->id)) {
                return new WP_Error(
                    'socialhub_bad_request',
                    __('User ID not found in the token', 'socialhub'),
                    array(
                        'status' => 403,
                    )
                );
            }
            return $token;
        } catch (Exception $e) {
            return new WP_Error(
               'socialhub_invalid_token',
               $e->getMessage(),
               array(
                   'status' => 403,
               )
            );
        }
    }

    /**
     * Filter to hook the rest_pre_dispatch, if the is an error in the request
     * send it, if there is no error just continue with the current request.
     *
     * @param $request
     */
    public function rest_pre_dispatch($request) {
        if (is_wp_error($this->jwt_error)) {
            return $this->jwt_error;
        }
        return $request;
    }

    /**
     * Filter to hook the rest_post_query applying a custom date_query_column
     * used by the before or after filter.
     *
     * @param $args
     * @param $request
     */
    public function rest_post_query($args, $request) {
        if (!isset($request['before']) && !isset($request['after'])) {
            return $args;
        }
        if(isset($request['date_query_column'])) {
            $args['date_query'][0]['column'] = $request['date_query_column'];
        }
        return $args;
    }

    /**
     * Filter to hook the rest_post_collection_params adding the custom
     * date_query_column for parameter discovery and schema validation.
     *
     * @param $query_params
     */
    public function rest_post_collection_params($query_params) {
      $query_params['date_query_column'] = [
          'description' => __( 'The date query column.' ),
          'type'        => 'string',
          'enum'        => [ 'post_date', 'post_date_gmt', 'post_modified', 'post_modified_gmt' ],
      ];
      return $query_params;
    }

    /**
     * Filter to hook the rest_comment_query applying a custom date_query_column
     * used by the before or after filter.
     *
     * @param $args
     * @param $request
     */
    public function rest_comment_query($args, $request) {
        if (!isset($request['before']) && !isset($request['after'])) {
            return $args;
        }
        if(isset($request['date_query_column'])) {
            $args['date_query'][0]['column'] = $request['date_query_column'];
        }
        return $args;
    }

    /**
     * Filter to hook the rest_comment_collection_params adding the custom
     * date_query_column for parameter discovery and schema validation.
     *
     * @param $query_params
     */
    public function rest_comment_collection_params($query_params) {
      $query_params['date_query_column'] = [
          'description' => __( 'The date query column.' ),
          'type'        => 'string',
          'enum'        => [ 'comment_date', 'comment_date_gmt' ],
      ];
      return $query_params;
    }

}
