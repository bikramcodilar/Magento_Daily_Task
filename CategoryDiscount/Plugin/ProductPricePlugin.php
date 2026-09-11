<?php
namespace Codilar\CategoryDiscount\Plugin;
use Magento\Catalog\Model\Product;

class ProductPricePlugin
{

    private const int CATEGORY_ID = 14;
    private const int DISCOUNT_PERCENT = 5;

    /**
     * @param Product $product
     * @param $result
     * @return mixed
     */
    public function afterGetFinalPrice(Product $product, $result): mixed
    {
        if (!$product->getId()) {
            return $result;
        }
        $categoryIds = $product->getCategoryIds();
        if (!in_array(self::CATEGORY_ID, $categoryIds)) {
            return $result;
        }
        $discount = $result * (self::DISCOUNT_PERCENT / 100);
        return $result - $discount;
    }
}
