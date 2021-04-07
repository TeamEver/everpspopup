{*
* Project : everpspopup
* @author Team EVER
* @copyright Team EVER
* @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
* @link https://www.team-ever.com
*}
<div class="ever_errors" style="display: none;">
    {if isset($ever_errors) && $ever_errors}
        <span id="ever_error_content">{$ever_errors|escape:'htmlall':'UTF-8'}</span>
    {/if}
</div>