<!-- Block aawishlist -->
<div id="aawishlist-block-home-{$product.id_product}" class="block svg-favorite" data-id-product="{$product.id_product}"
     data-id-customer="{$customer.id}" data-url-myaccount="{$urls.pages.my_account}">

  <div>
      {if ($is_favorite)}
          {hook h='displayFavoritesSvg' name='nav-wishlist'}
      {else}
          {hook h='displayFavoritesSvg' name='nav-wishlist-white'}
      {/if}

  </div>


</div>
<!-- /Block aawishlist -->
