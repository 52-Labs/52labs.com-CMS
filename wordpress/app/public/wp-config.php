<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          'g5XC.*r%W}|ry(4nZS>EJO[SfHoCC[PU:Hq u{%`Q->}x9TR$_^.>/[Ex{&B9vKC' );
define( 'SECURE_AUTH_KEY',   'uLq+;QL#$BF@WI}P:<A}*ZOm:h7~|4)Ve;W{&1;D>ZnbR$4Kf|>-xkf?CCC;nx->' );
define( 'LOGGED_IN_KEY',     'q*_P?kYdD<+}iW$X]h4%je+j)C7^BG)id>!GfoEj9/CFdJkU Xb,9Sim (;ua~cK' );
define( 'NONCE_KEY',         '`k9:(7Q<iV]gdGOp6{R{0[tZ- YaL=G+t9zNZX+20&I`N m:AO]afhJkqifT[*eo' );
define( 'AUTH_SALT',         '~US^*wfqO.:G@3+{NVTU:<2Aie2uY}K7V;7txKI(C1K`uCiC03(;U,K{Onr8(CY>' );
define( 'SECURE_AUTH_SALT',  ':d0gOG6j6ikKh I<9ybyVpLf9]d2]1#TF#NziaQvt/uOyeGzEbfwg/D+y<HrUq=d' );
define( 'LOGGED_IN_SALT',    'i<DtUZ?=%kxY(I<?-X;zfsgBxkL8{oWR<VVrz!@ryc !EWi#;5>GVI,n}9([vGOP' );
define( 'NONCE_SALT',        'rP+n[=>H.]y2=&:)CMFf}`>CFsk{e>EG[Ia3.<4`1~p!_J?}dFH{h`r%w=%Pv_CP' );
define( 'WP_CACHE_KEY_SALT', '#%3,QMv!-w?4kg&/>xT#:V6ju7wsyX`+t).{BzF-2$y[52PWyNI9+{xY!Q5i5giK' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
