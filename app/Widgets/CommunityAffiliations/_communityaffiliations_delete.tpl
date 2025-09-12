<section>
    <h3>{$c->__('communityaffiliation.delete_title')}</h3>
    <h4>{$server}/{$node}</h4>
    <br />
    {if="$clean"}
        <h4 class="gray">{$c->__('communityaffiliation.delete_clean_text')}</h4>
    {else}
        <h4 class="gray">{$c->__('communityaffiliation.delete_text')}</h4>
    {/if}
</section>
<footer>
    <button onclick="Dialog_ajaxClear()" class="button flat">
        {$c->__('button.cancel')}
    </button>
    <button
        name="submit"
        class="button flat color red"
        onclick="CommunityAffiliations_ajaxDeleteConfirm('{$server|echapJS}', '{$node|echapJS}'); Dialog_ajaxClear()">
        {$c->__('button.delete')}
    </button>
</footer>
