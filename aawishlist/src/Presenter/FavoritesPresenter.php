<?php


namespace Acdis\Favorites\Presenter;

class FavoritesPresenter
{
    private $language;


    public function __construct(\Language $language)
    {
        $this->language = $language;
    }

    public function present(Favorites $favorites)
    {
        return [
            'id'       => (int)$favorites->id,
            'name'     => $favorites->name[(int)$this->language->id],
            'position' => $favorites->position,
        ];
    }

    public function presentForFront(Favorites $favorites)
    {
        $idLang = \Context::getContext()->language->id;


        return [
            'id'             => $favorites->id_favorites,
            'push'           => $favorites->push,
            'name'           => $favorites->name[$idLang],
            'description'    => str_replace("\r\n", '<br/>', $favorites->description[$idLang]),
            'quote'          => $favorites->quote[$idLang],
            'custom_zone'    => $favorites->custom_zone[$idLang],
            'twitter_url'    => $favorites->twitter_url,
            'facebook_url'   => $favorites->facebook_url,
            'instagram_url'  => $favorites->instagram_url,
            'website_url'    => $favorites->website_url,
            'youtube_url'    => $favorites->youtube_url,
            'gallery_title'  => $favorites->gallery_title,
            'main_image'     => $favorites->getMainImageUrl(),
            'second_image'   => $favorites->getSecondImageUrl(),
            'homepage_image' => $favorites->getHomepageImageUrl(),
            'gallery_images' => $favorites->getGalleryImagesUrl(),
            'products'       => $this->getPresentedProducts($favorites->getProducts()),
            'url'            => $favorites->getUrl()
        ];
    }

    private function getPresentedProducts($productIds)
    {
        if (!empty($productIds)) {
            $assembler = new ProductAssembler(\Context::getContext());

            $presenterFactory     = new ProductPresenterFactory(\Context::getContext());
            $presentationSettings = $presenterFactory->getPresentationSettings();
            $presenter            = new ProductListingPresenter(
                new ImageRetriever(
                    \Context::getContext()->link
                ),
                \Context::getContext()->link,
                new PriceFormatter(),
                new ProductColorsRetriever(),
                \Context::getContext()->getTranslator()
            );

            $presentedProducts = [];

            if (is_array($productIds)) {
                $limit = 20;
                $count = 0;
                foreach ($productIds as $productId) {

                    if ($count > $limit) {
                        break;
                    }

                    $assembledProducts = $assembler->assembleProduct(['id_product' => $productId]);

                    if ($assembledProducts && !empty($assembledProducts)) {
                        $presentedProducts[] = $presenter->present(
                            $presentationSettings,
                            $assembledProducts,
                            \Context::getContext()->language
                        );
                        $count++;
                    }
                }
            }

            return $presentedProducts;
        }
        return [];
    }
}
