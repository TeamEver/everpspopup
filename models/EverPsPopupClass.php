<?php
/**
 * 2019-2023 Team Ever
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
 *  @copyright 2019-2023 Team Ever
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

class EverPsPopupClass extends ObjectModel
{
    public $id_everpspopup;
    public $id_shop;
    public $groups;
    public $newsletter;
    public $bgcolor;
    public $controller_array;
    public $categories;
    public $name;
    public $content;
    public $link;
    public $carrier;
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
                'validate' => 'isUnsignedInt',
                'required' => false
            ),
            'groups' => array(
                'type' => self::TYPE_STRING,
                'lang' => false,
                'validate' => 'isAnything',
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
                'validate' => 'isUnsignedInt',
                'required' => false
            ),
            'categories' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isAnything',
                'required' => false
            ),
            'carrier' => array(
                'type' => self::TYPE_INT,
                'lang' => false,
                'validate' => 'isUnsignedId',
                'required' => false
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
            'link' => array(
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isUrl'
            ),
            'name' => array(
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isGenericName'
            ),
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
        $customer_groups = Customer::getGroupsStatic(
            (int)Context::getContext()->customer->id
        );
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

            case 'order':
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
            $allowed_groups = json_decode($everpopup->groups);
            if ($everpopup->date_start !='0000-00-00') {
                if ($everpopup->date_start > $now
                    || $everpopup->date_end < $now) {
                    continue;
                }
            }
            if (!is_array($allowed_groups)
                || count($allowed_groups) <= 0
                || !array_intersect($allowed_groups, $customer_groups)
            ) {
                continue;
            }
            // 6 is all pages on shop
            if ((int)$id_controller == (int)$everpopup->controller_array || (int)$everpopup->controller_array == 6) {
                return $everpopup;
            }
        }
    }

    public static function getPopupGroups($id_everpspopup = false)
    {
        $ps_groups = Group::getGroups(
            (int)Context::getContext()->cookie->id_lang,
            (int)Context::getContext()->shop->id
        );
        $popup = new self(
            (int)$id_everpspopup
        );
        $popup_groups = [];
        foreach ($ps_groups as $ps_group) {
            $group = new Group(
                (int)$ps_group,
                (int)Context::getContext()->language->id,
                (int)Context::getContext()->shop->id
            );
            if (!in_array($group->id, $popup_groups)) {
                $popup_groups[$group->id] = array(
                    'id_group' => $group->id,
                    'name' => $group->name
                );
            }
        }
        foreach (json_decode($popup->groups) as $p_group) {
            $group = new Group(
                (int)$p_group,
                (int)Context::getContext()->language->id,
                (int)Context::getContext()->shop->id
            );
            if (!in_array($group->id, $popup_groups)) {
                $popup_groups[$group->id] = array(
                    'id_group' => $group->id,
                    'name' => $group->name
                );
            }
        }
        return $popup_groups;
    }
}
