{block name="frontend_vis_recommend_similar_products_outer_tabs"}
    <div class="tab--container-list">
        {block name="frontend_vis_recommend_similar_products_inner_tabs"}
            {if $sArticle.sSimilarArticles}
                {block name="frontend_vis_recommend_similar_products_tabs_similar"}
                    <div class="tab--container has--content is--active" data-tab-id="similar">
                        {block name="frontend_vis_recommend_similar_products_tabs_similar_inner"}
                            <div class="tab--header is--active">
                                <span class="tab--title" title="{s name="DetailRecommendationSimilarLabel" namespace="frontend/detail/index"}{/s}">
                                    {s name="DetailRecommendationSimilarLabel" namespace="frontend/detail/index"}{/s}
                                </span>
                            </div>
                            <div class="tab--content content--similar">
                                {block name="frontend_vis_recommend_similar_products_tabs_similar_slider_content"}
                                    <div class="similar--content">
                                        {include file="frontend/_includes/product_slider.tpl"
                                        articles=$sArticle.sSimilarArticles
                                        productBoxLayout="image"
                                        sliderItemMinWidth="250"}
                                    </div>
                                {/block}
                            </div>
                        {/block}
                    </div>
                {/block}
            {/if}
        {/block}
    </div>
{/block}
