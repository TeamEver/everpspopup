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

if (!defined('_PS_VERSION_')) {
    exit;
}

class EverpspopupAjaxAdultModeModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        $this->ajax = true;

        parent::initContent();
    }

    /**
     * Ajax Process
     */
    public function displayAjaxCheckAge()
    {
        if (empty(Tools::getValue('ever_birthday')) || !Validate::isDate(Tools::getValue('ever_birthday'))) {
            die(json_encode([
                'return' => false,
                'error' => $this->module->l('Birth ever_birthday is empty or is not valid.'),
            ]));
        }

        $from = new DateTime(Tools::getValue('ever_birthday'));
        $to = new DateTime('today');
        $age = $from->diff($to)->y;
        if ((bool) Configuration::get('EVERPSPOPUP_ASK_AGE') === false) {
            die(json_encode([
                'return' => true,
                'message' => $this->module->l('Vous êtes autorisé à visualiser le contenu'),
            ]));
        }
        if ($age >= (int)Configuration::get('EVERPSPOPUP_AGE')) {
            die(json_encode([
                'return' => true,
                'message' => $this->module->l('Vous êtes autorisé à visualiser le contenu'),
            ]));
        } else {
            die(json_encode([
                'return' => false,
                'error' => $this->module->l('Vous n\'êtes pas autorisé à visualiser le contenu'),
            ]));
        }

        die(json_encode([
            'return' => false,
            'error' => $this->module->l('Sorry, something went wrong. Please try again later'),
        ]));
    }
}
