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
define( 'DB_NAME', 'wordpress' );

/** MySQL database username */
define( 'DB_USER', 'phpmyadmin' );

/** MySQL database password */
define( 'DB_PASSWORD', 'stephino' );

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
define( 'AUTH_KEY',         '7i4Z^8|n-YN>#Ip:j@y2_%K|vJt>q|wU(Y,/f#eG^S4mu~DN>uMI@q!7tUm<kp57' );
define( 'SECURE_AUTH_KEY',  'NDad!~Mv9ePefsOMis`jb~k[7Zrc)BfjEE0YCfJlS<b%g5AlUSO:Gwry5j2W$.GK' );
define( 'LOGGED_IN_KEY',    'ZB1,Rt<JB9gGXW0aKAG@T$XzK3O#ei~1hD;OYM/gnlNqmC{<mi>eH_jH*O!bM`]t' );
define( 'NONCE_KEY',        '9=^)),;k4^W^]n`8aDB-wRVC? _u;YqG}#h=6+E[w7n*LGRqj^L~hCqi6r&T~Fcb' );
define( 'AUTH_SALT',        'A2 8v2uXW,RTz/hK)+I.Y>8x87I>q}dEz-}A8,4(g<S$b~ [d,Y.oi}+=cu+Xy)0' );
define( 'SECURE_AUTH_SALT', 'grg0Xg6$^j<ac(egQIN|rrmjk%p[#yp=}BL,oQpr;GH:XSqj.Cwcx8z~[fJr,pGX' );
define( 'LOGGED_IN_SALT',   'us3MG/R?0B!VNb@}MEbhE$eKnYKyncN7E{+)ww78AO.fY| 6C<j;M%NV}L/+3sKf' );
define( 'NONCE_SALT',       'WHw>;dr&8W,3CeRJ:*LTU]}VVkM`15d1uy@PNrrb5U*uS+c#[j$Xx 0M/u-4O ae' );

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
define( 'WP_DEBUG', true );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
