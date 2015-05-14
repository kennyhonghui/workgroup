<?php
/**
 * Some Configure here.
 */

# MySQL 设定 - 这部分资讯可以向您的主机服务商索取。
# Mysql 数据库名称。
define('DB_NAME', 'workgroup');
# MySQL 主机地址。
define('DB_HOST', 'localhost');
# Mysql 数据库用户名称。
define('DB_USER', 'root');
# Mysql 数据库用户登入密码。
define('DB_PASSWORD', '');
# Mysql 数据库预设使用编码设定。
define('DB_CHARSET', 'utf8');
# Mysql 数据库数据库表明前缀。
define('DB_PREFIX', 'wg_');
# Mysql 数据库连接使用端口号设定，若不确认，请勿更改。
define('DB_PORT', 3306);
# Mysql 数据库操作把手，可使用三种模式： PDO/mysqli/mysql。
# 若PHP版本高于5.2以上，建议使用PDO模式。
define('DB_DRIVER', 'PDO');

# 开启开发人员模式，此项专为开发人员调试时使用，为了保护程序安全，发布时请将此项设定为 false
define('WG_DEBUG', true);
# 是否保存已经成功执行的SQL语句队列开关设定，发布时请将此项设定为 false
define('WG_SAVEQUERIES', true);