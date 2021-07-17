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

class EverPsPopupClass extends ObjectModel
{
    public $id_everpspopup;
    public $id_shop;
    public $unlogged;
    public $newsletter;
    public $bgcolor;
    public $controller_array;
    public $categories;
    public $name;
    public $content;
    public $link;
    public $cookie_time;
    public $adult_mode;
    public $delay;
    public $date_start;
    public $date_end;
    public $active;
    public $id_lang;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'everpspopup',
        'primary' => 'id_everpspopup',
        'multilang' => true,
        'fields' => array(
            'id_shop' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isunsignedInt',
                'required' => true
            ),
            'unlogged' => array(
                'type' => self::TYPE_INT,
                'lang' => false,
                'validate' => 'isBool',
                'required' => false
            ),
            'newsletter' => array(
                'type' => self::TYPE_INT,
                'lang' => false,
                'validate' => 'isBool',
                'required' => false
            ),
            'bgcolor' => array(
                'type' => self::TYPE_STRING,
                'lang' => false,
                'validate' => 'isColor',
                'required' => false
            ),
            'controller_array' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isunsignedInt',
                'required' => false
            ),
            'categories' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isJson',
                'required' => false
            ),
            'name' => array(
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isGenericName'
            ),
            'link' => array(
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isUrl'
            ),
            'cookie_time' => array(
                'type' => self::TYPE_INT,
                'lang' => false,
                'validate' => 'isUnsignedId',
                'required' => true
            ),
            'adult_mode' => array(
                'type' => self::TYPE_INT,
                'lang' => false,
                'validate' => 'isBool',
                'required' => false
            ),
            'delay' => array(
                'type' => self::TYPE_INT,
                'lang' => false,
                'validate' => 'isUnsignedId',
                'required' => false
            ),
            'date_start' => array(
                'type' => self::TYPE_DATE,
                'lang' => false,
                'validate' => 'isDateFormat',
                'required' => false
            ),
            'date_end' => array(
                'type' => self::TYPE_DATE,
                'lang' => false,
                'validate' => 'isDateFormat',
                'required' => false
            ),
            'active' => array(
                'type' => self::TYPE_INT,
                'lang' => false,
                'validate' =>'isBool',
                'required' => false
            ),
            // lang fields
            'content' => array(
                'type' => self::TYPE_HTML,
                'lang' => true,
                'validate' => 'isCleanHtml'
            ),
        )
    );

    public function getPopup($id_shop, $id_lang)
    {
        if (!$id_shop) {
            $id_shop = (int)Context::getContext()->shop->id;
        }
        $sql = new DbQuery;
        $sql->select('*');
        $sql->from('everpspopup', 'ep');
        $sql->leftJoin(
            'everpspopup_lang',
            'epl',
            'ep.id_everpspopup = epl.id_everpspopup'
        );
        $sql->where('ep.active = 1');
        $sql->where('epl.id_lang = '.(int)$id_lang);

        return new self(
            (int)Db::getInstance()->getValue($sql),
            (int)$id_lang,
            (int)$id_shop
        );
    }

    public static function getPopups($id_shop, $id_lang)
    {
        if (!$id_shop) {
            $id_shop = (int)Context::getContext()->shop->id;
        }
        $sql = new DbQuery;
        $sql->select('*');
        $sql->from('everpspopup', 'ep');
        $sql->leftJoin(
            'everpspopup_lang',
            'epl',
            'ep.id_everpspopup = epl.id_everpspopup'
        );
        $sql->where('ep.active = 1');
        $sql->where('epl.id_lang = '.(int)$id_lang);

        return Db::getInstance()->executeS($sql);
    }

    public static function getPopupByIdController($id_shop, $id_lang, $controller)
    {
        switch ($controller) {
            case 'cms':
                $id_controller = 1;
                break;

            case 'product':
                $id_controller = 2;
                break;

            case 'category':
                $id_controller = 3;
                break;

            case 'index':
                $id_controller = 4;
                break;

            case 'cart':
                $id_controller = 5;
                break;

            case 'orderopc':
                $id_controller = 5;
                break;
            
            default:
                $id_controller = null;
                break;
        }
        $now = date('Y-m-d');
        $popups = self::getPopups(
            (int)$id_shop,
            (int)$id_lang
        );
        if (!$id_controller) {
            return false;
        }
        foreach ($popups as $popup_arr) {
            $everpopup = new self(
                (int)$popup_arr['id_everpspopup'],
                (int)$id_lang,
                (int)$id_shop
            );
            if ($everpopup->date_start !='0000-00-00') {
                if ($everpopup->date_start > $now) {
                    continue;
                }
                if ($everpopup->date_end < $now) {
                    continue;
                }
            }

            if ((int)$id_controller == (int)$everpopup->controller_array
                || (int)$everpopup->controller_array == 6
            ) {
                return $everpopup;
            }
        }
    }
}
