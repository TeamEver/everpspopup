<?php
/**
 * 2019-2021 Team Ever
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 *  @author    Team Ever <https://www.team-ever.com/>
 *  @copyright 2019-2021 Team Ever
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

/* Init */
$sql = array();

/* Create Tables in Database */
$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'everpspopup` (
         `id_everpspopup` int(10) unsigned NOT NULL auto_increment,
         `id_shop` int(10) unsigned NOT NULL,
         `unlogged` tinyint(1) unsigned DEFAULT NULL,
         `newsletter` tinyint(1) unsigned DEFAULT NULL,
         `bgcolor` varchar(255) DEFAULT NULL,
         `controller_array` int(10) unsigned DEFAULT NULL,
         `categories` varchar(255) DEFAULT NULL,
         `cookie_time` int(10) unsigned DEFAULT NULL,
         `adult_mode` int(10) unsigned DEFAULT NULL,
         `delay` int(10) unsigned DEFAULT NULL,
         `date_start` DATE DEFAULT NULL,
         `date_end` DATE DEFAULT NULL,
         `active` int(10) DEFAULT NULL,
         PRIMARY KEY (`id_everpspopup`))
         ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'everpspopup_lang` (
         `id_everpspopup` int(10) unsigned NOT NULL,
         `id_lang` int(10) unsigned NOT NULL,
         `name` varchar(255) DEFAULT NULL,
         `content` text DEFAULT NULL,
         `link` varchar(255) DEFAULT NULL,
         PRIMARY KEY (`id_everpspopup`, `id_lang`))
         ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

foreach ($sql as $s) {
    if (!Db::getInstance()->execute($s)) {
        return false;
    }
}
