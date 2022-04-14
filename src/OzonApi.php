<?php

namespace Tdkomplekt\OzonApi;

use Composer\Util\Http\Response;
use Tdkomplekt\OzonApi\Models\OzonCategory;
use Tdkomplekt\OzonApi\Models\OzonProduct;
use Tdkomplekt\OzonApi\Models\OzonTask;

class OzonApi
{
    protected string $language = 'DEFAULT';

    public function getCategoriesTree()
    {
        $url = 'https://api-seller.ozon.ru/v1/categories/tree';

        return $this->sendRequest($url);
    }

    public function getCategoryTree($categoryId)
    {
        $url = 'https://api-seller.ozon.ru/v2/category/tree';

        $data = [
            'category_id' => $categoryId,
            'language' => $this->language,
        ];

        return $this->sendRequest($url, $data);
    }

    public function getCategoryAttributes($categoryId)
    {
        $url = 'https://api-seller.ozon.ru/v3/category/attribute';

        $data = [
            'attribute_type' => 'ALL',
            'category_id' => [
                $categoryId
            ],
            'language' => $this->language,
        ];

        return $this->sendRequest($url, $data);
    }

    public function getCategoryAttributeValues($categoryId, $attributeId, $latsValueId = 0, $limit = 5000)
    {
        $url = 'https://api-seller.ozon.ru/v2/category/attribute/values';

        $data = [
            'attribute_id' => $attributeId,
            'category_id' => $categoryId,
            'language' => $this->language,
            'last_value_id' => $latsValueId,
            'limit' => $limit,
        ];

        return $this->sendRequest($url, $data);
    }

    public function getProductInfo(string $offerId = null, int $ozonProductId = null, int $ozonSku = null)
    {
        $url = 'https://api-seller.ozon.ru/v2/product/info';

        $data = [
            'offer_id' => $offerId ?: '',
            'product_id' => $ozonProductId ?: 0,
            'sku' => $ozonSku ?: 0,
        ];

        return $this->sendRequest($url, $data);
    }

    public function getProductImportInfo($taskId)
    {
        $url = 'https://api-seller.ozon.ru/v1/product/import/info';

        $data = [
            'task_id' => $taskId,
        ];

        return $this->sendRequest($url, $data);
    }

    public function importProduct(OzonProduct $ozonProduct)
    {
        $url = 'https://api-seller.ozon.ru/v2/product/import';

        $response = $this->sendRequest($url, $this->getProductData($ozonProduct));

        if ($response) {
            $data = json_decode($response, true);
            $task = OzonTask::firstOrCreate([
               'id' =>  $data['result']['task_id']
            ]);

            // todo run the task checking job
        }
        return $response;
    }

    public function getProductData(OzonProduct $ozonProduct): array // todo move in OzonProduct
    {
        return ['items' => [[

            'attributes' => $ozonProduct->getAttribute('attributes'),
            "complex_attributes" => $ozonProduct->getAttribute('complex_attributes'),

            "offer_id" => $ozonProduct->getAttribute('offer_id'),
            "category_id" => $ozonProduct->getAttribute('category_id'),
            "barcode" => $ozonProduct->getAttribute('barcode') ?? '',
            "name" => $ozonProduct->getAttribute('name'),

            "old_price" => (string) $ozonProduct->getAttribute('old_price') ?? '',
            "price" => (string) $ozonProduct->getAttribute('price') ?? '',
            "premium_price" => (string) $ozonProduct->getAttribute('premium_price') ?? '',
            "vat" => (string) $ozonProduct->getAttribute('vat') ?? "0.2",

            "weight" => $ozonProduct->getAttribute('weight'),
            "weight_unit" => $ozonProduct->getAttribute('weight_unit') ?? "g",

            "depth" => $ozonProduct->getAttribute('depth'),
            "height" => $ozonProduct->getAttribute('height'),
            "width" => $ozonProduct->getAttribute('width'),
            "dimension_unit" => $ozonProduct->getAttribute('dimension_unit') ?? "mm",

            "primary_image" => $ozonProduct->getAttribute('primary_image') ?? "",
            "images" => $ozonProduct->getAttribute('images') ?? [],
            "images360" => $ozonProduct->getAttribute('images360') ?? [],
            "color_image" => $ozonProduct->getAttribute('color_image') ?? "",
            "pdf_list" => $ozonProduct->getAttribute('pdf_list') ?? []

        ]]];
    }

    protected function sendRequest($url, array $data = null)
    {
        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        if ($data) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $headers = array(
            "X-Custom-Header: value",
            "Content-Type: application/json",
            "Client-Id: ".config('ozon-api.client_id'),
            "Api-Key: ".config('ozon-api.api_key'),
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }
}
