{*
* Project : everpspopup
* @author Team EVER
* @copyright Team EVER
* @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
* @link https://www.team-ever.com
*}

<a href="#everpspopup_block_center" {if isset($everpspopup->carrier) && $everpspopup->carrier > 0}data-carrier="{$everpspopup->carrier|escape:'htmlall':'UTF-8'}"{/if} rel="nofollow" data-fancybox id="ever_fancy_mark"></a>
<div id="everpspopup_block_center" {if isset($everpspopup->carrier) && $everpspopup->carrier > 0}data-carrier="{$everpspopup->carrier|escape:'htmlall':'UTF-8'}"{/if} data-delay="{$everpspopup->delay|escape:'htmlall':'UTF-8'}" data-adult="{$everpspopup->adult_mode|escape:'htmlall':'UTF-8'}" data-expire="{$everpspopup->cookie_time|escape:'htmlall':'UTF-8'}" data-cookiesuffix="{$everpspopup->controller_array|escape:'htmlall':'UTF-8'}{$everpspopup->cookie_suffix|escape:'htmlall':'UTF-8'}" class="Everpopup_block" style="display:none;">
    {if $everpspopup->link}<a href="{$everpspopup->link|escape:'htmlall':'UTF-8'}" rel="nofollow">{/if}
        <div class="container"{if $everpspopup->bgcolor} style="background-color:{$everpspopup->bgcolor|escape:'htmlall':'UTF-8'};"{/if}>
            <div class="row">
                {if $everpspopup->content}<div class="rte col-12 col-xs-12">{$everpspopup->content nofilter}</div>{/if}
            </div>
        </div>
    {if $everpspopup->link}</a>{/if}
{if $everpspopup->newsletter}
    <section>
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-md-offset-3">
                    <hgroup>
                        <p class="h2 text-center">
                            {l s='Subscribe for newsletter' mod='everpspopup'}
                        </p>
                    </hgroup>
                    <div class="well center-block">
                        <form id="ever_subscription_form" method="post">
                            <div class="input-group col-md-12">
                                <div class="form-group">
                                    <label for="everpspopupEmail">{l s='Your email' mod='everpspopup'}</label>
                                    <input id="everpspopupEmail" name="everpspopupEmail" class="form-control" aria-describedby="everpspopupEmailHelp" type="email" placeholder="{l s='Your email' mod='everpspopup'}" required />
                                    <small id="everpspopupEmailHelp" class="form-text text-muted">{l s='We\'ll never share your email with anyone else.' mod='everpspopup'}</small>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="everpspopupGdpr" name="everpspopupGdpr" aria-describedby="everpspopupGdprHelp" value="1">
                                    <label class="form-check-label" for="everpspopupGdpr">{l s='GDPR consent' mod='everpspopup'}</label>
                                    <small id="everpspopupGdprHelp" class="form-text text-muted">{l s='You can unsubscribe anytime. You will find our contact information for this in the terms of use of the site.' mod='everpspopup'}</small>
                                </div>
                                  <input type="hidden" id="everpspopup_new_subscribe_url" value="{$link->getModuleLink('everpspopup', 'ajaxNewSubscribe')|escape:'htmlall':'UTF-8'}" />
                                   <button class="btn btn-info btn-lg" type="submit">{l s='Submit' mod='everpspopup'}</button>
                              </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
{/if}
{if $everpspopup->adult_mode && $ever_ask_age == true}
    <section>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <hgroup>
                        <h2>
                            {l s='You must be of age to access this content' mod='everpspopup'}
                        </h2>
                    </hgroup>
                    <div class="well text-center center-block">
                        <form id="adult_mode_form" method="post">
                            <div class="input-group col-md-12">
                                <input class="input-lg" id="ever_birthday" name="ever_birthday" type="date" placeholder="{l s='Birthday' mod='everpspopup'}" required />
                                  <input type="hidden" id="everpspopup_new_adult_url" value="{$link->getModuleLink('everpspopup', 'ajaxAdultMode')|escape:'htmlall':'UTF-8'}" />
                                   <button class="btn btn-info btn-lg" type="submit">{l s='Submit' mod='everpspopup'}</button>
                              </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
{/if}
{if $everpspopup->adult_mode && $ever_ask_age == false}
    <section>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <hgroup>
                        <h2>
                            {l s='You must be of age to access this content' mod='everpspopup'}
                        </h2>
                    </hgroup>
                    <div class="well text-center center-block">
                        <form id="adult_mode_form" method="post">
                            <div class="input-group col-md-12">
                                <input class="input-lg" id="ever_birthday" name="ever_birthday" type="hidden" value="{$ever_required_age|escape:'htmlall':'UTF-8'}" />
                                  <input type="hidden" id="everpspopup_new_adult_url" value="{$link->getModuleLink('everpspopup', 'ajaxAdultMode')|escape:'htmlall':'UTF-8'}" />
                                   <button class="btn btn-info btn-lg" type="submit">{l s='I certify that I am of age' mod='everpspopup'}</button>
                              </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
{/if}
    <div class="col-md-12 alert alert-success" id="everpspopup_confirm" style="display:none;">
    </div>
    <div class="col-md-12 alert alert-warning" id="everpspopup_error" style="display:none;">
    </div>
</div>
