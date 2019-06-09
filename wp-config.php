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
define( 'DB_NAME', 'wpc' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'root' );

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
define( 'AUTH_KEY',         's+fSgFns;}Juge_[gsufxlG_+lE,Zm*q[S@M;uSDI>>mb>?!@Bo<|}i-(s+/:EKX' );
define( 'SECURE_AUTH_KEY',  'w.h24/%oo6(41:Kx6oa$CC*Q[l>eqQc(n&n%5szH,%o_T@v1mR<pRDVPh]<XWHQj' );
define( 'LOGGED_IN_KEY',    'utk.h~oSi61MztoN#lwp>{}.Y;{`m!l#H6Sro$8]U?{k-~[F^lT^CZ,QUs#^l7I8' );
define( 'NONCE_KEY',        'P7{ jQH33d:#g~D?m ..S@sClVxg-=8)*24G($K4jm/p-+{}p:+w9(<+9/BHD:fo' );
define( 'AUTH_SALT',        'ck1GqJ+y9$r$[]5fnKc tAs^0#F@~8OL!7_yz<b#SOh}U&9=I@ ^52oD%gR*Biv ' );
define( 'SECURE_AUTH_SALT', '4[OpW0@MU|}$GSHp4<6%|~ck29|)aoM<lPfK75UzmV1U1U@#C-/q>wnH;+}E{oIB' );
define( 'LOGGED_IN_SALT',   'QIqH3Pu*n)7s=IE:kSi;7fZHfKg-.l|&v$#hq3}4ZaJ]0;ZB~JW!h?3m^>N}Z!,/' );
define( 'NONCE_SALT',       'G.i9<1Tk+c,L%(P#e:2R81q^&<ly2Y5Jk:R02?_<:f-UD<0WqD2eQtE6x^v,G*ge' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wpc_';

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

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
