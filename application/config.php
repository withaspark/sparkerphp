<?php
/**
 * Set global configuration options here
 */

/**
 * Location relative to document root that app's index.php lives
 * Example,
 *    define('__APPPATH__', '/');
 * Include trailing slash.
 */
define('__APPPATH__', '/');

/**
 * Title of site to put in HTML template header title
 */
define('__APPTITLE__', 'SparkerPHP Framework');

/**
 * Database for PDO setup
 * Example:
 *    define('__DATABASE__', 'sqlite:database/mydb.sq3');
 */
define('__DATABASE__', '<YOUR DB>');

/**
 * Default route when no route defined
 */
define('__DEFAULTROUTE__', 'default');

/**
 * Display debugging and logging info
 * For production, set to false
 * For development/testing, set to true
 */
define('__DEBUG__', true);

/**
 * Set the error reporting level
 */
error_reporting(E_ALL & ~E_STRICT);

/**
 * Turn off php displaying of errors
 * For production, set to FALSE
 * For development/testing, set to TRUE
 */
ini_set('display_errors', TRUE);
?>
