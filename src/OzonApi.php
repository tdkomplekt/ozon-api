<?php

namespace Tdkomplekt\OzonApi;

use Tdkomplekt\OzonApi\Models\OzonAttribute;
use Tdkomplekt\OzonApi\Models\OzonCategory;

class OzonApi
{
    public function getCategoriesTreeArray()
    {
        $response = $this->sendRequestCategoriesTree();

        $dataArray = [];
        if ($response) {
            $dataArray = json_decode($response, true);
        }

        return $dataArray['result'] ?? [];
    }

    public function getAttributes($categoryId, $language = 'DEFAULT'): ?array
    {
        $response = $this->sendRequestAttributes($categoryId, $language);

        $dataArray = [];
        if ($response) {
            $dataArray = json_decode($response, true);
        }

        return $dataArray['result'][0]['attributes'] ?? [];
    }

    private function sendRequestCategoriesTree()
    {
        $url = 'https://api-seller.ozon.ru/v1/categories/tree';
        return $this->sendRequest($url);
    }

    public function getCategoryTree($categoryId, $language = 'DEFAULT')
    {
        $url = 'https://api-seller.ozon.ru/v2/category/tree';

        $data = [
            'category_id' => $categoryId,
            'language' => $language,
        ];

        return $this->sendRequest($url, $data);
    }

    public function sendRequestAttributes($categoryId, $language = 'DEFAULT')
    {
        $url = 'https://api-seller.ozon.ru/v3/category/attribute';

        $data = [
            'attribute_type' => 'ALL',
            'category_id' => [
                $categoryId
            ],
            'language' => $language,
        ];

        return $this->sendRequest($url, $data);
    }

    private function sendRequest($url, $data = null)
    {
        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        if($data) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $headers = array(
            "X-Custom-Header: value",
            "Content-Type: application/json",
            "Client-Id: " . config('ozon-api.client_id'),
            "Api-Key: ". config('ozon-api.api_key'),
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }

    public function syncAttributes()
    {
        foreach (OzonCategory::all() as $category) {
            $attributesTreeArray = $this->getAttributes($category->id);

            foreach ($attributesTreeArray as $attribute) {

                $attribute = OzonAttribute::firstOrCreate([
                    'id' =>  $attribute['id'],
                ], [
                    'name' => $attribute['name'],
                    'description' => $attribute['description'],
                    'type' => $attribute['type'],
                    'is_collection' => $attribute['is_collection'],
                    'is_required' => $attribute['is_required'],
                    'group_id' => $attribute['group_id'],
                    'group_name' => $attribute['group_name'],
                    'dictionary_id' => $attribute['dictionary_id'],
                ]);

                $category->attributes()->save($attribute, [
                    'ozon_category_id' => $category->id,
                    'ozon_attribute_id' => $attribute->id,
                ]);
            }
        }
    }

    public function syncCategories()
    {
        $categoriesTreeArray = $this->getCategoriesTreeArray();

        foreach ($categoriesTreeArray as $category) {
            $this->addCategory($category['category_id'], $category['title'], $category['children']);
        }
    }

    private function addCategory($categoryId, $title, $childrenArray = null, $parentCategoryId = 0)
    {
        OzonCategory::firstOrCreate([
            'id' => $categoryId
        ], [
            'title' => $title,
            'parent_id' => $parentCategoryId
        ]);

        if ($childrenArray) {
            foreach ($childrenArray as $subCategory) {
                $this->addCategory($subCategory['category_id'], $subCategory['title'], $subCategory['children'], $categoryId);
            }
        }
    }

    public function fillCategoriesCustomFields()
    {
        $categories = OzonCategory::with(['children', 'parent.parent'])->get();

        $categories->each(function ($category) {
            $category->search = mb_strtoupper($category->getFullTitle(';'));
            $category->last_node = $category->children->count() == 0;
            $category->save();
        });
    }
}
