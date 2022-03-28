{*
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
*}

<div class="panel row">
    <h3><i class="icon icon-smile"></i> {l s='Ever Popup' mod='everpspopup'}</h3>
    <div class="col-md-6">
        <img id="everlogo" src="{$everpspopup_dir|escape:'htmlall':'UTF-8'}/logo.png" style="max-width: 120px;">
        <p>
            <strong>{l s='Welcome to Ever Popup !' mod='everpspopup'}</strong><br />
        </p>
        <p>
            {l s='Thanks for using Team Ever\'s modules' mod='everpspopup'}.<br />
            <strong>{l s='If you want to use newsletter subscription form, please make sure default Prestashop newsletter module is installed' mod='everpspopup'}</strong><br />
        </p>
        {if isset($popup_admin_link) && $popup_admin_link}
        <a href="{$popup_admin_link|escape:'htmlall':'UTF-8'}" class="btn btn-lg btn-success">{l s='Manage popups' mod='everpspopup'}</a>
        {/if}
        {if isset($module_link) && $module_link}
        <a href="{$module_link|escape:'htmlall':'UTF-8'}" class="btn btn-lg btn-success">{l s='Module configuration' mod='everpspopup'}</a>
        {/if}
    </div>
    <div class="col-md-6">
            <p class="alert alert-warning">
                {l s='This module is free and will always be ! You can support our free modules by making a donation by clicking the button below' mod='everpspopup'}
            </p>
            <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top" style="display: flex;justify-content: center;">
            <input type="hidden" name="cmd" value="_s-xclick" />
            <input type="hidden" name="hosted_button_id" value="3LE8ABFYJKP98" />
            <input type="image" src="https://www.team-ever.com/wp-content/uploads/2019/06/appel_a_dons-1.jpg" border="0" name="submit" title="Soutenez le développement des modules gratuits de Team Ever !" alt="Soutenez le développement des modules gratuits de Team Ever !" style="width: 150px;" />
            <img alt="" border="0" src="https://www.paypal.com/fr_FR/i/scr/pixel.gif" width="1" height="1" />
            </form>
    </div>
</div>
