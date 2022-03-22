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

            foreach ($attributesTreeArray as $attribute) {
                $attribute = OzonAttribute::firstOrCreate([
                    'id' => $attribute['id'],
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

                $category->attributes()->attach($attribute, [
                    'ozon_category_id' => $category->id,
                    'ozon_attribute_id' => $attribute->id,
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
