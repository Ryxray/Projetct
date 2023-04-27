document.addEventListener('DOMContentLoaded', () => {
  getDataFavorites();
});

function getDataFavorites() {
  const pageFavorite = document.getElementById('page-favorite');

  document.addEventListener('click', function (e) {
    let target = e.target.closest('.svg-favorite');
    if (target) {
      const idProduct = target.getAttribute('data-id-product');
      const idCustomer = target.getAttribute('data-id-customer');
      const urlMyAccount = target.getAttribute('data-url-myaccount');

      if(idCustomer !='') {
        addToWishlist(idProduct, idCustomer, pageFavorite, target)
      }
      else {
        document.location.href=urlMyAccount;
      }

      if (pageFavorite) {
        let productListingFavorite = target.closest('article');
        productListingFavorite.classList.add("favorite-product-" + idProduct);
      }
    }
  })

}

function addToWishlist(idProduct, idCustomer, pageFavorite, product) {

  const data = {
    fc: 'module',
    module: 'aawishlist',
    controller: 'wishlistajax',
    ajax: 1,
    action: 'addToWishlist',
    idProduct: idProduct,
    idCustomer: idCustomer,
  }

  $.ajax({
    url: prestashop.urls.base_url + 'index.php',
    method: 'POST',
    data: data,
    dataType: 'json',
  }).then(res => {

    let colorSvg = product.getElementsByTagName('svg')[0];
    colorSvg = colorSvg.getAttribute('data-id-name');
    const navWishList = document.getElementById("aawishlist-block-home-" + idProduct).getElementsByClassName(colorSvg)[0].getElementsByTagName('polygon')[0];

    if (res && res.success) {

      if (res['success'] == 1) {
        navWishList.setAttribute("fill", '#052d3a');
        navWishList.setAttribute("stroke", '#052d3a');
      } else if (res['success'] == 2) {

        navWishList.setAttribute("fill", "#ebeff0");
        navWishList.setAttribute("stroke", "#ebeff0");

        if (pageFavorite) {
          const productSelectFavorite = document.getElementsByClassName("favorite-product-" + idProduct);
          productSelectFavorite[0].parentNode.removeChild(productSelectFavorite[0]);
        }

      }
    }
  })
}





