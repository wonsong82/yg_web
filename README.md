# README #

start from outside root which is parent of server
cd yg
yg> git clone https://boksil@bitbucket.org/ygpresents/web.git

this creates web folder and pulls source
yg>cd web

yg/web> tar -xvf latest.tar.gz --strip-components=1
this unzips core wordpress files
yg/web> git reset --hard
this resets and brings sources over written by previous unzip to up-to-date  

to setup local db user :
>create user yguser@'%' identified by '1gobaesong';

>grant all privileges on ygdb.* to 'yguser'@'*' identified by '1gobaesong';

>flush privileges;


below is the initial wp-config.php content.  copy and create it in the web folder
/* -------------------------------------------------- */
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
define('DB_NAME', 'ygdb');

/** MySQL database username */
define('DB_USER', 'yguser');

/** MySQL database password */
define('DB_PASSWORD', '1gobaesong');

/** MySQL hostname */
define('DB_HOST', '23.23.176.180)');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         './GUKq4@EvCr}}@(!f[B(Xv>tk.|tX|FxBE#vB4Y[8!9M&Z -Kg;+xw$|s gNRe}');
define('SECURE_AUTH_KEY',  's4^6+LHtf!@yPI:np[=it$o_kg1D}jTjh6:V@sGeR`TLIK]?99g=eWv_ORtv-wsL');
define('LOGGED_IN_KEY',    'YL+$W|YU#Dy+]!J)}Elqd^ZE3.Jr7sr-t,Xzr@uA}sw+,e%L=+>;+Iv^|juFRf&4');
define('NONCE_KEY',        'rZJ#LUo}[n~iu} %-/N`2lR~t+y>DMXF(RO6oNNse:+Lg]<g- V T y/r6~}TAF0');
define('AUTH_SALT',        ',K<u$nxu-?:-q/Pk|5wBI1U>lMyaX+E8@@]Yw*eO3Lvs7DM*CJ4SFc-Z]EZ1s5!/');
define('SECURE_AUTH_SALT', 'If-|<rx+D&5+fx&_6;<I|q1a4eksGp%*0#m7n#oy#!&-jaC7}jY$XFKz2g=d+?^U');
define('LOGGED_IN_SALT',   'Mb]$6/+keE(tq:<Cf4|xxFusa5)tlr%w_I4_/H O+^bGoh/->&-C/PAfeWgbzLMg');
define('NONCE_SALT',       'N5F5,RQl;`C}a(BqUabq8zQtMbma-i6tSh2c_9tX&IiFKI7l#aS{a:]:_sKTiimY');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

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
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');