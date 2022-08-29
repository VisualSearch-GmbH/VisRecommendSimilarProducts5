{block name="frontend_vis_recommend_similar_products_tabs_navigation"}
    <div class="tab--navigation">
        {block name="frontend_vis_recommend_similar_products_tabs_navigation_inner"}
            {block name="frontend_vis_recommend_similar_products_tabs_entry_similar_products"}
                {if $sArticle.sSimilarArticles|count > 0}
                    <a href="#content--similar-products" title="{s name="DetailRecommendationSimilarLabel" namespace="frontend/detail/index"}{/s}" class="tab--link has--content is--active">
                        {s name="DetailRecommendationSimilarLabel" namespace="frontend/detail/index"}{/s}
                    </a>
                {/if}
            {/block}
        {/block}
    </div>
{/block}
