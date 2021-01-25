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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'ase' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

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
define( 'AUTH_KEY',         '~Z[p&Ng@)isTo>jX*aQOb1vL9`L(~V;b y>bbQk1SnfikG,C2C-R:X8us H{4g|3' );
define( 'SECURE_AUTH_KEY',  '$S7VUua~Ky?rw2ybXz`qU8S~?TlOjBFqfN?$K[cCt>n2oomt2+g{M`w#IFK@4!U_' );
define( 'LOGGED_IN_KEY',    'UPEE2`=yE}dZXa[#P:xVh<!*9KZUIl0ssT9Xy[<&DY,=&COJ ezkksEUOqe& Ngm' );
define( 'NONCE_KEY',        ')J0>;p8~aO/keoz[V2V5GR$coqpAOBLZ@Qv) v&eAi3@UlvXCBCl*NHl[^%R~r?l' );
define( 'AUTH_SALT',        'CY?R#ymw{B[BnU+F<I4i&+<XB_*b0%1$k,Bn&R2ieBF]U6p|1Z&!pxqUICwN!|ix' );
define( 'SECURE_AUTH_SALT', 'ti@Aa9D0fbuR)<)&bU}LxR-]j&L$!>uw[xvu{C# um;r8NtU/N;2w3q$<WP/T4?$' );
define( 'LOGGED_IN_SALT',   '1iBVz9/#$6PMPMoL5~+V:[aF%U!K:,%@XIg^f[c~]uw8q#{}h{`?fUYr66LFMUl<' );
define( 'NONCE_SALT',       'x?CD/o}cmS@b]5%-V&s13Z<xzV &XB7!`fH,E@+=6M(JBvvL*.D{aNoWA[~i}Z|p' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_ase';

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
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
