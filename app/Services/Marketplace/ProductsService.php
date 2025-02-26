<?php

namespace App\Services\Marketplace;

class ProductsService extends MarketplaceService
{
    /**
     * @return mixed|void
     */
    public function getCategories()
    {
        return $this->get('/api/v2/shop/taxons', 200);
    }

    /**
     * @param string $taxon
     * @return mixed|void
     */
    public function getProductsByTaxon(string $taxon = '')
    {
        $query = [
            'page' => '1',
            'itemsPerPage' => '30',
            'productTaxons.taxon.code' => $taxon,
        ];

        return $this->get('/api/v2/shop/products?' . http_build_query($query), 200);
    }

    /**
     * @param string $code
     * @return mixed|void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getProduct(string $code = '')
    {
        return $this->get('/api/v2/shop/products/' . $code, 200);
    }

    public function getProductBySlug(string $productName)
    {
        $slug = $this->toSlug($productName);

        return $this->get('/api/v2/shop/products-by-slug/' . $slug, 200);
    }

    public function getVariants()
    {
        $query = [
            'page' => '1',
            'itemsPerPage' => '30'
        ];

        return $this->get('/api/v2/shop/product-variants?' . http_build_query($query), 200);
    }

    public function getProductVariants($productVariants)
    {
        $variants = [];
        foreach ($productVariants as $variant) {
            $variants[] = $this->getVariant($variant);
        }

        return $variants;
    }

    public function getVariant(string $code)
    {
        return $this->get($code, 200);
    }

    /**
     * @param array $products
     * @return array
     */
    public function getImagesForLatestProducts($products)
    {
        $productsWithImages = [];
        foreach ($products as $product) {
            $images = $this->getProductImages($product->images);
            $product->images = $images;
            $productsWithImages[] = $product;
        }
        return $productsWithImages;
    }

    public function getDefaultVariantForLatestProducts($products)
    {
        $productsWithDefaultVariant = [];

        foreach ($products as $product) {
            $defaultVariant = $this->getVariant($product->defaultVariant);
            $product->defaultVariant = $defaultVariant;
            $productsWithDefaultVariant[] = $product;
        }
//        dd($productsWithVariants);
        return $productsWithDefaultVariant;
    }

    /**
     * @param array $products
     * @return array
     */
    public function getProductImages(array $images)
    {
//        dd($images);
        $imagePaths = [];
        foreach ($images as $image) {
            if ($image->path !== null) {
                $response = $this->getPhoto($image->path);
//                dd($response);
                $imagePaths[] = $this->EncodeToBase64($response);
//                $imagePaths[] =$response;
            }
        }
        return $imagePaths;
    }

    public function getProductsImageForCartSummary(array $items)
    {
        $products = [];
        $itemsWithImg = [];

        foreach ($items as $product) {
            $products[] = $this->getProductBySlug($product['productName']);
        }

        $productsWithImages = $this->getImagesForLatestProducts($products);

        foreach ($items as $product => $item) {
            $productImg = $productsWithImages[$product];
            $itemWithImg = $item;
            $itemWithImg['image'] = $productImg->images[0];

            if (is_string($item['variant'])) {
                $itemWithImg['variant'] = $this->responseToArray($this->getVariant($item['variant']));
            }

            $itemsWithImg[] = $itemWithImg;
        }

        return $itemsWithImg;
    }


    private function getPhoto($url)
    {
        $this->url = config('services.marketplace.url');
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->url.$url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($curl);
        curl_close($curl);
        return $data;
    }

    /**
     * @param
     * @return mixed|void
     */
    public function getLatestProducts()
    {
        $query = [
            'order[createdAt]' => 'desc',
            'page' => '1',
            'itemsPerPage' => '30'
        ];

//        dd($this->get('/api/v2/shop/products?' . http_build_query($query), 200));
        $response = $this->get('/api/v2/shop/products?' . http_build_query($query), 200);
        return  $response;
    }

    /**
     * @param mixed $data
     * @return mixed|void
     */
    public function getSubCategories(mixed $data)
    {
        $subCategories = [];
        foreach ($data as $category) {
            foreach ($category->children as $subCategory) {
                $subCategories[] = $this->get($subCategory, 200);

            }
            $category->children = $subCategories;
            $subCategories = [];
        }
        return $data;
    }
}
