<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'practwordp_89994d529ba24b8f1dbad177_database' );

/** MySQL database username */
define( 'DB_USER', 'pmnpbxdvfy' );

/** MySQL database password */
define( 'DB_PASSWORD', '6844KTOH67UE147R$' );

/** MySQL hostname */
define( 'DB_HOST', 'practwordp-89994d529ba24b8f1dbad177-dbserver.mysql.database.azure.com' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '86Oifd*3as~6pWT~3$Qm.9J_aT OH{pzwn}H<daOtG~jZlM7!WQSG#^ef7myPwVO' );
define( 'SECURE_AUTH_KEY',  '/)Vsq1EgPZyw`P^;YqfM#h<Ua)||Qzv`>:8m)(m~C@5y]`hD9${9d!w[q:%kVo{3' );
define( 'LOGGED_IN_KEY',    'n{@##bLY1n;[WK<1cbcQ(8S_RqAIqaKp6kyUp.DtKMFJ^f+R*7%aG?~N^k,bmEbG' );
define( 'NONCE_KEY',        'hPQqz{&{kPt|=k*l]0~7hVvEMD/@ f@-#-YkX4f?~,WqZ)e[.^ m{O3!B6@8#),k' );
define( 'AUTH_SALT',        'I>q #L;T30TR5AQq?B3E-g9CXM*3C?>_C4+s~oC39&Z0+OaXFNs_F2Y2)}q`6!p5' );
define( 'SECURE_AUTH_SALT', 'yLr2`$ck Djw{w5jrpTImRH,t<rzATbPN?Eo`FA{WMNWzA5tute9{got5rof9E:!' );
define( 'LOGGED_IN_SALT',   'Gl;%Y.2r;tn,9}nL6Sf$uebhM@zv@w#I!5X-rt0sQpr]v_ q Iz(JZF+V3u^m&_B' );
define( 'NONCE_SALT',       'u:1G:U_dIXG%+uMjjmJeCZ)zeOHfWj4V.<:5P_xdWP0|tIS2`4YTbNMq58ToB*m4' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );
define( 'AUTOMATIC_UPDATER_DISABLED', true );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        