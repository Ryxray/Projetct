{extends file='page.tpl'}

{block name='page_content'}
  <div id="page-favorite" class="products" >
      {foreach from=$tab_product item="product"}
          {block name='product_miniature'}
              {include file='catalog/_partials/miniatures/product.tpl' product=$product}
          {/block}
      {/foreach}
  </div>
{/block}

