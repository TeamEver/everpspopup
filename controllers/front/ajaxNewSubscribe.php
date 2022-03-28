<?php
/**
 * 2019-2022 Team Ever
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
 *  @copyright 2019-2022 Team Ever
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_.'everpspopup/everpspopup.php';

class EverpspopupAjaxNewSubscribeModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        $this->isSeven = Tools::version_compare(_PS_VERSION_, '1.7', '>=') ? true : false;
        $this->ajax = true;

        parent::initContent();
    }

    /**
     * Ajax Process
     */
    public function displayAjaxNewSubscribe()
    {
        $module = new Everpspopup();
        $ps_activeNewsletter = false;
        if ($this->isSeven) {
            $ps_activeNewsletter = Module::isEnabled('ps_emailsubscription');
        } else {
            $ps_activeNewsletter = Module::isEnabled('blocknewsletter');
        }

        if ($ps_activeNewsletter) {
            if (empty($_SERVER['REMOTE_ADDR'])
                || !filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP)
            ) {
                die(json_encode(array(
                    'return' => false,
                    'error' => $module->l('User ip not found or not valid', 'everpspopup')
                )));
            }

            if (!Tools::getValue('everpspopupEmail')
                || !Validate::isEmail(Tools::getValue('everpspopupEmail'))
            ) {
                die(json_encode(array(
                    'return' => false,
                    'error' => $module->l('Mail address is empty or is not valid.', 'everpspopup')
                )));
            }

            if (!Tools::getValue('everpspopupGdpr')) {
                die(json_encode(array(
                    'return' => false,
                    'error' => $module->l('GDPR consent.', 'everpspopup')
                )));
            }

            // Get needed vars
            $user_ip = $_SERVER['REMOTE_ADDR'];
            $user_email = pSQL(Tools::getValue('everpspopupEmail'));

            // Check if email address already exists
            if ($this->isSeven) {
                $table_newsletter_name = 'emailsubscription';
                $id_group_shop = (int)Context::getContext()->shop->id_shop_group;
            } else {
                $id_group_shop = (int)Context::getContext()->shop->id_group;
                $table_newsletter_name = 'newsletter';
            }
            $sql = new DbQuery();
            $sql->select('id');
            $sql->from($table_newsletter_name);
            $sql->where("email = '{$user_email}'");

            $subscribed = Db::getInstance()->getValue($sql);

            if ($subscribed) {
                // if user already subscribe, make sure active is true
                $sql = "UPDATE "._DB_PREFIX_."$table_newsletter_name SET active = 1 WHERE email = '{$user_email}'";
                $activate = Db::getInstance()->execute($sql);
                if (!$activate) {
                    die(json_encode(array(
                        'return' => false,
                        'error' => $module->l('Error : Can\'t update the newsletter subscription', 'ajaxNewSubscribe')
                    )));
                }

                die(json_encode(array(
                    'return' => true,
                    'message' => $module->l('You\'ve already registered to our mailing list', 'ajaxNewSubscribe')
                )));
            }
            if ($this->isSeven) {
                Hook::exec(
                    'actionNewsletterRegistrationBefore',
                    [
                        'hookName' => 'everpspopup',
                        'email' => $user_email,
                        'action' => 'subscribe',
                        'error' => false,
                    ]
                );
            }

            // Add new email address to newsletter table
            $newSubscription = Db::getInstance()->insert(
                $table_newsletter_name,
                array(
                    'id_shop' => (int)Context::getContext()->shop->id,
                    'id_shop_group' => (int)$id_group_shop ,
                    'email' => pSQL($user_email),
                    'newsletter_date_add' => (new DateTime)->format('Y-m-d H:i:s'),
                    'ip_registration_newsletter' => pSQL($user_ip),
                    'active' => 1,
                )
            );

            if ($newSubscription) {
                if ($this->isSeven && Configuration::get('NW_CONFIRMATION_EMAIL')) {
                    $this->sendConfirmationEmail($user_email);
                    if ($code = Configuration::get('NW_VOUCHER_CODE')) {// send voucher
                        $this->sendVoucher($user_email, $code);
                    }
                    // hook
                    Hook::exec(
                        'actionNewsletterRegistrationAfter',
                        [
                            'hookName' => 'everpspopup',
                            'email' => $user_email,
                            'action' => 'subscribe',
                            'error' => false,
                        ]
                    );
                }
                die(json_encode(array(
                    'return' => true,
                    'message' => $module->l('Thank you ! Your e-mail has been successfuly registered.')
                )));
            }

            die(json_encode(array(
                'return' => false,
                'error' => $module->l('Sorry, something went wrong. Please try again later.')
            )));
        } else {
            die(json_encode(array(
                'return' => true,
                'message' => $module->l('Module Newsletter not activated')
            )));
        }
    }

    /**
     * Send a confirmation email.
     *
     * @param string $email
     *
     * @return bool
     */
    protected function sendConfirmationEmail($email)
    {
        $language = new Language($this->context->language->id);

        return Mail::Send(
            $this->context->language->id,
            'newsletter_conf',
            $this->trans(
                'Newsletter confirmation',
                array(),
                'Emails.Subject',
                $language->locale
            ),
            array(),
            pSQL($email),
            null,
            null,
            null,
            null,
            null,
            _PS_MODULE_DIR_.'ps_emailsubscription/mails/',
            false,
            $this->context->shop->id
        );
    }

    /**
     * Send an email containing a voucher code.
     *
     * @param $email
     * @param $code
     *
     * @return bool|int
     */
    protected function sendVoucher($email, $code)
    {
        $language = new Language($this->context->language->id);

        return Mail::Send(
            $this->context->language->id,
            'newsletter_voucher',
            $this->trans(
                'Newsletter voucher',
                array(),
                'Emails.Subject',
                $language->locale
            ),
            array(
                '{discount}' => $code,
            ),
            $email,
            null,
            null,
            null,
            null,
            null,
            _PS_MODULE_DIR_.'ps_emailsubscription/mails/',
            false,
            $this->context->shop->id
        );
    }
}
