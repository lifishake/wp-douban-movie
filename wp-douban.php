<?php
/*
Plugin Name: WP-Douban-Movie
Plugin URI: https://github.com/lifishake/wp-douban-movie
Description: 豆瓣内容列表展示。因为豆瓣API已被停止使用，所以现作者修改成只对豆瓣电影有效。感谢原作者<a href="https://fatesinger.com/project/wordpress-plugin.html">大发</a>（Bigfa）。
Version: 1.0.0
Author: 大致
Author URI: https://pewae.com
*/

define('WPD_VERSION', '1.0.0');
define('WPD_URL', plugins_url('', __FILE__));
define('WPD_PATH', dirname( __FILE__ ));
define('WPD_ADMIN_URL', admin_url());
define('WPD_CACHE_TIME', 60*60*24*30);
define('WPD_CACHE_KEY', 'WPD');

/**
 * 加载函数
 */
require WPD_PATH . '/functions.php';

/**
 * 插件激活,新建数据库
 */
register_activation_hook(__FILE__, 'wpd_install');

