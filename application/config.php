<?php if (!defined('INCLUDES_OK')) die ('Invalid SparkerPHP configuration.');
/**
 * Set global configuration options here
 */

/**
 * Location relative to document root that app's index.php lives
 * Example,
 *    define('__APPPATH__', '/');
 * Include trailing slash.
 */
if (!defined('__APPPATH__')) define('__APPPATH__', '/');

/**
 * Title of site to put in HTML template header title
 */
if (!defined('__APPTITLE__')) define('__APPTITLE__', 'SparkerPHP Framework');

/**
 * Database for PDO setup
 * Example:
 *    define('__DATABASE__', 'sqlite:database/mydb.sq3');
 */
if (!defined('__DATABASE__')) define('__DATABASE__', '');

/**
 * Require all $_GET and $_POST parameters to meet schema requirements
 * If using SparkerPHP positive security schema based system, set to true
 * If want to allow all parameters, set to false
 */
if (!defined('__SAFESCHEMA__')) define('__SAFESCHEMA__', true);

/**
 * Default route when no route defined
 */
if (!defined('__DEFAULTROUTE__')) define('__DEFAULTROUTE__', 'default');

/**
 * Display debugging and logging info
 * For production, set to false
 * For development/testing, set to true
 */
if (!defined('__DEBUG__')) define('__DEBUG__', true);
?>
