{extends file="parent:frontend/detail/tabs/similar.tpl"}

{block name="frontend_detail_index_similar_slider_content"}
    <div class="similar--content">
        {include file="frontend/_includes/product_slider.tpl"
        articles=$sArticle.sSimilarArticles
        sliderInitOnEvent="onShowContent-similar"
        productBoxLayout="image"
        sliderItemMinWidth="250"}
    </div>
{/block}
