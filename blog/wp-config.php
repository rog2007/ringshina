<?php
/**
 * Основные параметры WordPress.
 *
 * Этот файл содержит следующие параметры: настройки MySQL, префикс таблиц,
 * секретные ключи, язык WordPress и ABSPATH. Дополнительную информацию можно найти
 * на странице {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Кодекса. Настройки MySQL можно узнать у хостинг-провайдера.
 *
 * Этот файл используется сценарием создания wp-config.php в процессе установки.
 * Необязательно использовать веб-интерфейс, можно скопировать этот файл
 * с именем "wp-config.php" и заполнить значения.
 *
 * @package WordPress
 */

// ** Параметры MySQL: Эту информацию можно получить у вашего хостинг-провайдера ** //
/** Имя базы данных для WordPress */
define('DB_NAME', 'gb_newdb_ring');

/** Имя пользователя MySQL */
define('DB_USER', 'gb_newdb_ring');

/** Пароль к базе данных MySQL */
define('DB_PASSWORD', '17a941ae1xvn');

/** Имя сервера MySQL */
define('DB_HOST', 'mysql48.1gb.ru');

/** Кодировка базы данных для создания таблиц. */
define('DB_CHARSET', 'utf8');

/** Схема сопоставления. Не меняйте, если не уверены. */
define('DB_COLLATE', '');

/**#@+
 * Уникальные ключи и соли для аутентификации.
 *
 * Смените значение каждой константы на уникальную фразу.
 * Можно сгенерировать их с помощью {@link https://api.wordpress.org/secret-key/1.1/salt/ сервиса ключей на WordPress.org}
 * Можно изменить их, чтобы сделать существующие файлы cookies недействительными. Пользователям потребуется снова авторизоваться.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '&I !G{O$oz/e-(6V@IiKhRvleu/jk4+^dCk4,@W>!+5%+.eeR^!8CZ)hbrl!1=rI');
define('SECURE_AUTH_KEY',  '&cq)O;&ySj6V8>a(4DmR=`Fhs}c-r8-8$*Vw$9~=W3w_9 YsTpD];J5*P0u/{9R#');
define('LOGGED_IN_KEY',    'WaXBWw+OMbc^^51`OOM5VRBjKx^]z<|i#(_~YB#Wdt<#tY0!0)(CL%Y:q)t:<<<S');
define('NONCE_KEY',        'qO5`3Mm@o1V)+D|t_!*0|N&{W(#SCWzfL.R[c!Y8M;2`#X^b-S%XVr?$Tm+R.:V|');
define('AUTH_SALT',        ';,)$4F-kpgBz*RX3B)R|ZzA_jHr$v :+~J(rvGw+A%]X#0B,!KaxaeEwQ:=a?^Yx');
define('SECURE_AUTH_SALT', ',Bvw3WCn5Ri|GB#+u_4|;B?H8%nj7l?-(G9D{E-$N{+;|RCE~@+Z%N-i:9x}cI.-');
define('LOGGED_IN_SALT',   '#ng0T@?#|%<j&w/rnh3$O _72L _Sw=N`^O$1xynGrz(`Mm24Y*JQJm]45TcK#l}');
define('NONCE_SALT',       '>D|C5P=k2aNs+?*5[.rI$N %|%-;y ?1Gr<(-|h3qpNfWY[L<,Qr%dPEu.6]1-Zu');

/**#@-*/

/**
 * Префикс таблиц в базе данных WordPress.
 *
 * Можно установить несколько блогов в одну базу данных, если вы будете использовать
 * разные префиксы. Пожалуйста, указывайте только цифры, буквы и знак подчеркивания.
 */
$table_prefix  = 'wprsh_';

/**
 * Язык локализации WordPress, по умолчанию английский.
 *
 * Измените этот параметр, чтобы настроить локализацию. Соответствующий MO-файл
 * для выбранного языка должен быть установлен в wp-content/languages. Например,
 * чтобы включить поддержку русского языка, скопируйте ru_RU.mo в wp-content/languages
 * и присвойте WPLANG значение 'ru_RU'.
 */
define('WPLANG', 'ru_RU');

/**
 * Для разработчиков: Режим отладки WordPress.
 *
 * Измените это значение на true, чтобы включить отображение уведомлений при разработке.
 * Настоятельно рекомендуется, чтобы разработчики плагинов и тем использовали WP_DEBUG
 * в своём рабочем окружении.
 */
define('WP_DEBUG', false);

/* Это всё, дальше не редактируем. Успехов! */

/** Абсолютный путь к директории WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Инициализирует переменные WordPress и подключает файлы. */
require_once(ABSPATH . 'wp-settings.php');
