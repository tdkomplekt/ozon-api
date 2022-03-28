<?php

namespace Tdkomplekt\OzonApi\Console\Commands;

use Tdkomplekt\OzonApi\Base\Command;
use Tdkomplekt\OzonApi\Models\OzonAttribute;
use Tdkomplekt\OzonApi\Models\OzonCategory;

class SyncAttributes extends Command
{
    protected $signature = 'ozon:sync-attributes';

    public function handle(): int
    {
        $startTime = now();
        $this->syncAttributes();
        $endTime = now();

        return $this->success($startTime, $endTime);
    }

    protected function syncAttributes()
    {
        foreach (OzonCategory::all() as $category) {
            $attributesTreeArray = $this->getAttributes($category->id);

            foreach ($attributesTreeArray as $attributeData) {
                $attribute = OzonAttribute::firstOrCreate([
                    'id' => $attributeData['id'],
                ], [
                    'name' => $attributeData['name'],
                    'description' => $attributeData['description'],
                    'type' => $attributeData['type'],
                    'is_collection' => $attributeData['is_collection'],
                    'dictionary_id' => $attributeData['dictionary_id'],
                ]);

                $category->attributes()->attach($attribute, [
                    'ozon_category_id' => $category->id,
                    'ozon_attribute_id' => $attribute->id,

                    'is_required' => $attributeData['is_required'],
                    'group_id' => $attributeData['group_id'],
                    'group_name' => $attributeData['group_name'],
                ]);
            }
        }
    }

    protected function getAttributes($categoryId): ?array
    {
        $response = $this->ozonApi->getCategoryAttributes($categoryId);

        $dataArray = [];
        if ($response) {
            $dataArray = json_decode($response, true);
        }

        return $dataArray['result'][0]['attributes'] ?? [];
    }
}
