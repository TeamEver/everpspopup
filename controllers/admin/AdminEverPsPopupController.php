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

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_.'everpspopup/models/EverPsPopupClass.php';
class AdminEverPsPopupController extends ModuleAdminController
{
    private $html;
    const POPUP_IMG  = _PS_MODULE_DIR_.'everpspopup/views/img/';
    const POPUP_VIEWS  = _PS_MODULE_DIR_.'everpspopup/views/';
    public function __construct()
    {
        $this->bootstrap = true;
        $this->lang = true;
        $this->table = 'everpspopup';
        $this->module_name = 'everpspopup';
        $this->className = 'EverPsPopupClass';
        $this->context = Context::getContext();
        $this->identifier = 'id_everpspopup';
        $this->isSeven = Tools::version_compare(_PS_VERSION_, '1.7', '>=') ? true : false;
        $this->context->smarty->assign(array(
            'everpspopup_dir' => _MODULE_DIR_ . '/everpspopup/'
        ));
        $this->success = array();
        $this->fields_list = array(
            'id_everpspopup' => array(
                'title' => $this->l('ID'),
                'align' => 'left',
                'width' => 'auto'
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'align' => 'left',
                'width' => 'auto'
            ),
            'active' => array(
                'title' => $this->l('Status'),
                'type' => 'bool',
                'active' => 'active',
                'orderby' => false,
                'class' => 'fixed-width-sm'
            )
        );

        $this->colorOnBackground = true;
        $module_link  = 'index.php?controller=AdminModules&configure=everpspopup&token=';
        $module_link .= Tools::getAdminTokenLite('AdminModules');
        $this->context->smarty->assign(array(
            'module_link' => $module_link
        ));

        parent::__construct();
    }

    public function l($string, $class = null, $addslashes = false, $htmlentities = true)
    {
        if ($this->isSeven) {
            return Context::getContext()->getTranslator()->trans(
                $string,
                [],
                'Modules.Everpspopup.Admineverpspopupcontroller'
            );
        }

        return parent::l($string, $class, $addslashes, $htmlentities);
    }

    /**
     * Gestion de la toolbar
     */
    public function initPageHeaderToolbar()
    {
        //Bouton d'ajout
        $this->page_header_toolbar_btn['new'] = array(
            'href' => self::$currentIndex . '&add' . $this->table . '&token=' . $this->token,
            'desc' => $this->l('Add new element'),
            'icon' => 'process-icon-new'
        );
        parent::initPageHeaderToolbar();
    }

    public function renderList()
    {
        $this->toolbar_title = $this->l('Popup configuration');
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected items'),
                'confirm' => $this->l('Delete selected items ?')
            ),
        );
        if (Tools::getIsset('deleteeverpspopup')) {
            $everObj = new EverPsPopupClass(
                (int)Tools::getValue('id_everpspopup')
            );
            $everObj->delete();
        }
        if (Tools::getIsset('activeeverpspopup')) {
            $everObj = new EverPsPopupClass(
                (int)Tools::getValue('id_everpspopup')
            );
            (int)$everObj->active = !(int)$everObj->active;
            $everObj->save();
        }
        if (Tools::isSubmit('submitBulkdelete'.$this->table)) {
            $this->processBulkDelete();
        }
        if (Tools::isSubmit('submitBulkdisableSelection'.$this->table)) {
            $this->processBulkDisable();
        }
        if (Tools::isSubmit('submitBulkenableSelection'.$this->table)) {
            $this->processBulkEnable();
        }

        $lists = parent::renderList();

        $this->html .= $this->context->smarty->fetch(self::POPUP_VIEWS.'templates/admin/header.tpl');
        $module_instance = Module::getInstanceByName($this->module_name);
        if ($module_instance->checkLatestEverModuleVersion($this->module_name, $module_instance->version)) {
            $this->html .= $this->context->smarty->fetch(
                _PS_MODULE_DIR_
                .'/'
                .$this->module_name
                .'/views/templates/admin/upgrade.tpl'
            );
        }
        if (count($this->errors)) {
            $this->context->smarty->assign(array(
                'errors' => $this->errors
            ));
            $this->html .= $this->context->smarty->fetch(self::POPUP_VIEWS.'templates/admin/errors.tpl');
        }
        if (count($this->success)) {
            $this->context->smarty->assign(array(
                'success' => $this->success
            ));
            $this->html .= $this->context->smarty->fetch(self::POPUP_VIEWS.'templates/admin/success.tpl');
        }
        $this->html .= $lists;
        $this->html .= $this->context->smarty->fetch(self::POPUP_VIEWS.'templates/admin/footer.tpl');

        return $this->html;
    }

    public function renderForm()
    {
        if (count($this->errors)) {
            return false;
        }
        $everpopup = $this->loadObject(true);
        // Check if obj exists
        if (Validate::isLoadedObject($everpopup)) {
            $selected_cat = json_decode($everpopup->categories);
            $selected_cat = str_replace('[', '', $everpopup->categories);
            $selected_cat = str_replace(']', '', $selected_cat);
            $selected_cat = str_replace('"', '', $selected_cat);
            $selected_cat = explode(',', $selected_cat);
            if (!is_array($selected_cat)) {
                $selected_cat = array($selected_cat);
            }
            $tree = array(
                'selected_categories' => $selected_cat,
                'use_search' => true,
                'use_checkbox' => true,
                'id' => 'id_category_tree',
            );
        } else {
            $selected_cat = array();
            $tree = array(
                'selected_categories' => $selected_cat,
                'use_search' => true,
                'use_checkbox' => true,
                'id' => 'id_category_tree',
            );
        }

        // build conditions array
        $showCondition = array(
            array(
                'id_option' => 1,
                'name' => $this->l('CMS only')
            ),
            array(
                'id_option' => 2,
                'name' => $this->l('Products only')
            ),
            array(
                'id_option' => 3,
                'name' => $this->l('Categories only')
            ),
            array(
                'id_option' => 4,
                'name' => $this->l('Home only')
            ),
            array(
                'id_option' => 5,
                'name' => $this->l('Cart page only')
            ),
            array(
                'id_option' => 6,
                'name' => $this->l('All')
            ),
        );

        // build cookie_time array
        $cookie_time = array(
            array(
              'id_option' => 1,
              'name' => $this->l('1 day')
            ),
            array(
              'id_option' => 2,
              'name' => $this->l('2 days')
            ),
            array(
              'id_option' => 3,
              'name' => $this->l('3 days')
            ),
            array(
              'id_option' => 4,
              'name' => $this->l('4 days')
            ),
            array(
              'id_option' => 5,
              'name' => $this->l('5 days')
            ),
            array(
              'id_option' => 7,
              'name' => $this->l('7 days')
            ),
            array(
              'id_option' => 10,
              'name' => $this->l('10 days')
            ),
            array(
              'id_option' => 15,
              'name' => $this->l('15 days')
            ),
            array(
              'id_option' => 20,
              'name' => $this->l('20 days')
            ),
            array(
              'id_option' => 30,
              'name' => $this->l('30 days')
            ),
            array(
              'id_option' => 60,
              'name' => $this->l('60 days')
            ),
            array(
              'id_option' => 0,
              'name' => $this->l('Disabled')
            )
        );

        // Building the Add/Edit form
        $this->fields_form = array(
            'tinymce' => true,
            'description' => $this->l('Add a new popup'),
            'submit' => array(
                'name' => 'save',
                'title' => $this->l('Save'),
                'class' => 'btn button pull-right'
            ),
            'buttons' => array(
                'import' => array(
                    'name' => 'save_and_stay',
                    'type' => 'submit',
                    'class' => 'btn btn-default pull-right',
                    'icon' => 'process-icon-save',
                    'title' => $this->l('Save & stay')
                ),
            ),
            'input' => array(
                array(
                    'type' => 'categories',
                    'name' => 'categories',
                    'label' => $this->l('Category'),
                    'desc' => $this->l('Set popup only in specific categories'),
                    'hint' => $this->l('Leave empty for no use'),
                    'tree' => $tree,
                ),
                array(
                    'type' => 'group',
                    'label' => $this->l('Group access'),
                    'name' => 'groupBox',
                    'values' => Group::getGroups(Context::getContext()->language->id),
                    'desc' => $this->l('Popup will be shown to these groups'),
                    'hint' => $this->l('Please select at least one customer group'),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Where to show the popup ?'),
                    'desc' => $this->l('Choose default controller for this popup'),
                    'hint' => $this->l('Set "All" for all pages on front-office'),
                    'name' => 'controller_array',
                    'required' => true,
                    'options' => array(
                        'query' => $showCondition,
                        'id' => 'id_option',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Lifetime of the cookie (in days)'),
                    'desc' => $this->l('If disabled, the popup will show systematically'),
                    'hint' => $this->l('Set 0 or disable for debug'),
                    'name' => 'cookie_time',
                    'required' => true,
                    'options' => array(
                        'query' => $cookie_time,
                        'id' => 'id_option',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show newsletter subscription form ?'),
                    'desc' => $this->l('You must have Prestashop newsletter module'),
                    'hint' => $this->l('Form won\'t appear if Prestashop module is not set'),
                    'name' => 'newsletter',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => true,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => false,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'desc' => $this->l('Won\'t appear on front-office'),
                    'hint' => $this->l('Useful and required on back-office'),
                    'required' => true,
                    'name' => 'name',
                    'lang' => true
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Popup content'),
                    'desc' => $this->l('Type popup content'),
                    'hint' => $this->l('Will appear in front-office'),
                    'required' => true,
                    'name' => 'content',
                    'lang' => true,
                    'autoload_rte' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Popup link'),
                    'desc' => $this->l('Will make all popup cliquable'),
                    'hint' => $this->l('Leave empty for no use'),
                    'name' => 'link',
                    'lang' => true
                ),
                array(
                    'type' => 'color',
                    'label' => $this->l('Popup content background color'),
                    'desc' => $this->l('Will change popup content color'),
                    'hint' => $this->l('Not all the popup, only content'),
                    'name' => 'bgcolor',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Adult mode'),
                    'desc' => $this->l('Set yes for asking user birthday'),
                    'hint' => $this->l('Will block screen'),
                    'name' => 'adult_mode',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => true,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => false,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Delay'),
                    'desc' => $this->l('Delay before popup appears'),
                    'hint' => $this->l('Value must be in milliseconds'),
                    'name' => 'delay'
                ),
                array(
                    'type' => 'date',
                    'label' => $this->l('Date start'),
                    'desc' => $this->l('Date popup will start to appear'),
                    'hint' => $this->l('Leave empty for no use'),
                    'name' => 'date_start',
                ),
                array(
                    'type' => 'date',
                    'label' => $this->l('Date end'),
                    'desc' => $this->l('Date popup will end'),
                    'hint' => $this->l('Leave empty for no use'),
                    'name' => 'date_end',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Active'),
                    'desc' => $this->l('Enable popup'),
                    'hint' => $this->l('Set no for not activating this popup'),
                    'name' => 'active',
                    'lang' => false,
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),
            )
        );
        $groups = Group::getGroups($this->context->language->id);
        $category_groups_ids = (array)json_decode($everpopup->groups);
        foreach ($groups as $group) {
            $this->fields_value['groupBox_'.$group['id_group']] = Tools::getValue('groupBox_'.$group['id_group'], (in_array($group['id_group'], $category_groups_ids)));
        }
        $lists = parent::renderForm();

        $this->html .= $this->context->smarty->fetch(
            self::POPUP_VIEWS.'templates/admin/header.tpl'
        );
        $this->html .= $lists;
        if (count($this->errors)) {
            foreach ($this->errors as $error) {
                $this->html .= Tools::displayError($error);
            }
        }
        $this->html .= $this->context->smarty->fetch(
            self::POPUP_VIEWS.'templates/admin/configure.tpl'
        );
        $this->html .= $this->context->smarty->fetch(
            self::POPUP_VIEWS.'templates/admin/footer.tpl'
        );

        return $this->html;
    }

    public function postProcess()
    {
        parent::postProcess();
        if ($this->isSeven) {
            $ps_newsletter = Module::isInstalled('ps_emailsubscription');
        } else {
            $ps_newsletter = Module::isInstalled('blocknewsletter');
        }
        if (Tools::isSubmit('save') || Tools::isSubmit('save_and_stay')) {
            if (Tools::getValue('unlogged')
                && !Validate::isBool(Tools::getValue('unlogged'))
            ) {
                 $this->errors[] = $this->l('Unlogged is not valid');
            }
            if (Tools::getValue('newsletter')
                && !Validate::isBool(Tools::getValue('newsletter'))
            ) {
                 $this->errors[] = $this->l('Newsletter is not valid');
            }
            if (Tools::getValue('newsletter')
                && !$ps_newsletter
            ) {
                 $this->errors[] = $this->l('Newsletter module is not installed');
            }
            if (Tools::getValue('bgcolor')
                && !Validate::isColor(Tools::getValue('bgcolor'))
            ) {
                 $this->errors[] = $this->l('Color is not valid');
            }
            if (Tools::getValue('controller_array')
                && !Validate::isAnything(json_encode(Tools::getValue('controller_array')))
            ) {
                 $this->errors[] = $this->l('Controller is not valid');
            }
            if (Tools::getValue('controller_array')
                && !Validate::isUnsignedInt(Tools::getValue('controller_array'))
            ) {
                 $this->errors[] = $this->l('Controller is not valid');
            }
            if (Tools::getValue('categories')
                && !Validate::isArrayWithIds(Tools::getValue('categories'))
            ) {
                 $this->errors[] = $this->l('Controller is not valid');
            }
            if (!Tools::getValue('groupBox')
                || !Validate::isArrayWithIds(Tools::getValue('groupBox'))
            ) {
                $groups = Group::getGroups(
                    (int)Context::getContext()->language->id,
                    (int)$shop['id_shop']
                );
                $group_condition = array();
                foreach ($groups as $group) {
                    $group_condition[] = (int)$group['id_group'];
                }
            }
            if (Tools::getValue('cookie_time')
                && !Validate::isUnsignedInt(Tools::getValue('cookie_time'))
            ) {
                 $this->errors[] = $this->l('Cookie time is not valid');
            }
            if (Tools::getValue('adult_mode')
                && !Validate::isBool(Tools::getValue('adult_mode'))
            ) {
                 $this->errors[] = $this->l('Adult mode is not valid');
            }
            if (Tools::getValue('delay')
                && !Validate::isUnsignedInt(Tools::getValue('delay'))
            ) {
                 $this->errors[] = $this->l('Delay is not valid');
            }
            if (Tools::getValue('date_start')
                && !Validate::isDateFormat(Tools::getValue('date_start'))
            ) {
                 $this->errors[] = $this->l('Date start is not valid');
            }
            if (Tools::getValue('date_end')
                && !Validate::isDateFormat(Tools::getValue('date_end'))
            ) {
                 $this->errors[] = $this->l('Date end is not valid');
            }
            if (Tools::getValue('active')
                && !Validate::isBool(Tools::getValue('active'))
            ) {
                 $this->errors[] = $this->l('Active is not valid');
            }

            if (Tools::getValue('id_everpspopup')) {
                $everpopup = new EverPsPopupClass(
                    (int)Tools::getValue('id_everpspopup')
                );
            } else {
                $everpopup = new EverPsPopupClass();
            }
            $everpopup->id_shop = (int)Context::getContext()->shop->id;
            $everpopup->unlogged = (int)Tools::getValue('unlogged');
            if (isset($group_condition)) {
                $everpopup->groups = json_encode($group_condition);
            } else {
                $everpopup->groups = json_encode(Tools::getValue('groupBox'));
            }
            $everpopup->newsletter = (int)Tools::getValue('newsletter');
            $everpopup->bgcolor = Tools::getValue('bgcolor');
            $everpopup->controller_array = (int)Tools::getValue('controller_array');
            $everpopup->categories = json_encode(Tools::getValue('categories'));
            $everpopup->cookie_time = (int)Tools::getValue('cookie_time');
            $everpopup->delay = (int)Tools::getValue('delay');
            $everpopup->date_start = Tools::getValue('date_start');
            $everpopup->date_end = Tools::getValue('date_end');
            $everpopup->adult_mode = (int)Tools::getValue('adult_mode');
            $everpopup->active = (int)Tools::getValue('active');
            foreach (Language::getLanguages(false) as $language) {
                if (!Tools::getIsset('name_'.$language['id_lang'])
                    || !Validate::isGenericName(Tools::getValue('name_'.$language['id_lang']))
                ) {
                    $this->errors[] = $this->l('Name is not valid for lang ').$language['id_lang'];
                } else {
                    $everpopup->name[$language['id_lang']] = Tools::getValue('name_'.$language['id_lang']);
                }
                if (Tools::getValue('content_'.$language['id_lang'])
                    && !Validate::isCleanHtml(Tools::getValue('content_'.$language['id_lang']))
                ) {
                    $this->errors[] = $this->l('Content is not valid for lang ').$language['id_lang'];
                } else {
                    $everpopup->content[$language['id_lang']] = Tools::getValue('content_'.$language['id_lang']);
                }
                if (!Tools::getIsset('link_'.$language['id_lang'])
                    && !Validate::isUrl(Tools::getValue('link_'.$language['id_lang']))
                ) {
                    $this->errors[] = $this->l('Link is not valid for lang ').$language['id_lang'];
                } else {
                    $everpopup->link[$language['id_lang']] = Tools::getValue('link_'.$language['id_lang']);
                }
            }
            if (!count($this->errors)) {
                if ($this->isSeven) {
                    $saved = $everpopup->save();
                } else {
                    if (!Tools::getValue('id_everpspopup')) {
                        $saved = $everpopup->save();
                    } else {
                        // Quick fix for fuckin' PS 1.6 not updating object
                        $saved = true;
                        $saved &= Db::getInstance()->update(
                            $this->table,
                            array(
                                'active' => (int)Tools::getValue('active'),
                                'id_shop' => (int)$this->context->shop->id,
                                'unlogged' => (int)Tools::getValue('unlogged'),
                                'groups' => json_encode(Tools::getValue('groupBox')),
                                'newsletter' => (int)Tools::getValue('newsletter'),
                                'bgcolor' => Tools::getValue('bgcolor'),
                                'controller_array' => (int)Tools::getValue('controller_array'),
                                'categories' => json_encode(Tools::getValue('categories')),
                                'cookie_time' => (int)Tools::getValue('cookie_time'),
                                'delay' => (int)Tools::getValue('delay'),
                                'date_start' => Tools::getValue('date_start'),
                                'date_end' => Tools::getValue('date_end'),
                                'adult_mode' => (int)Tools::getValue('adult_mode'),
                                'active' => (int)Tools::getValue('active')
                            ),
                            'id_everpspopup = '.(int)Tools::getValue('id_everpspopup')
                        );
                        foreach (Language::getLanguages(false) as $language) {
                            $saved &= Db::getInstance()->update(
                                $this->table.'_lang',
                                array(
                                    'name' => Tools::getValue('name_'.$language['id_lang']),
                                    'content' => Tools::getValue('content_'.$language['id_lang']),
                                    'link' => Tools::getValue('link_'.$language['id_lang']),
                                ),
                                'id_everpspopup = '.(int)Tools::getValue('id_everpspopup').' AND id_lang = '.$language['id_lang']
                            );
                        }
                    }
                }
                if ((bool)$saved === true) {
                    $this->success[] = $this->l('Popup has been fully saved');
                    if (Tools::isSubmit('save_and_stay') === true) {
                        Tools::redirectAdmin(
                            self::$currentIndex
                            .'&updateeverpspopup=&id_everpspopup='
                            .(int)$everpopup->id
                            .'&token='
                            .$this->token
                        );
                    } else {
                        Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
                    }
                } else {
                    $this->errors[] = $this->l('Can\'t update the current object');
                }
            }
        }
    }

    protected function processBulkDelete()
    {
        foreach (Tools::getValue($this->table.'Box') as $idObj) {
            $everpopup = new EverPsPopupClass((int)$idObj);

            if (!$everpopup->delete()) {
                $this->errors[] = $this->l('An error has occurred: Can\'t delete the current object');
            } else {
                $this->errors[] = $this->l('Objects has been fully deleted');
            }
        }
    }

    protected function processBulkDisable()
    {
        foreach (Tools::getValue($this->table.'Box') as $idObj) {
            $everpopup = new EverPsPopupClass((int)$idObj);
            if ($everpopup->active) {
                $everpopup->active = false;
            }

            if (!$everpopup->save()) {
                $this->errors[] = $this->l('An error has occurred: Can\'t delete the current object');
            } else {
                $this->html .= $this->l('Objects has been fully disabled');
            }
        }
    }

    protected function processBulkEnable()
    {
        foreach (Tools::getValue($this->table.'Box') as $idObj) {
            $everpopup = new EverPsPopupClass((int)$idObj);
            if (!$everpopup->active) {
                $everpopup->active = true;
            }

            if (!$everpopup->save()) {
                $this->errors[] = $this->l('An error has occurred: Can\'t delete the current object');
            } else {
                $this->errors[] = $this->l('Objects has been fully enabled');
            }
        }
    }
}
