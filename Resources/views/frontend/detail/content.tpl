{extends file="parent:frontend/detail/content.tpl"}

{block name="frontend_detail_index_detail"}
    {block name="frontend_detail_index_visually_similar_articles_tab"}
        {if $visChangePosition}
            {include file="frontend/vis_recommend_similar_products5/detail/content/similars.tpl"}
        {/if}
    {/block}
    {$smarty.block.parent}
{/block}

{block name="frontend_detail_index_tabs_cross_selling"}
    {if !$visChangePosition}
        <div class="vis-tab-menu--cross-selling">
    {/if}
        {$smarty.block.parent}
    {if !$visChangePosition}
        </div>
    {/if}
{/block}