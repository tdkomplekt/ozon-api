<?php

namespace Tdkomplekt\OzonApi\Console\Commands;

use DB;
use Tdkomplekt\OzonApi\Base\Command;
use Tdkomplekt\OzonApi\Models\OzonAttribute;
use Tdkomplekt\OzonApi\Models\OzonBrand;
use Tdkomplekt\OzonApi\Models\OzonCategory;
use Tdkomplekt\OzonApi\Models\OzonAttributeOption;

class SyncOptions extends Command
{
    protected $signature = 'ozon:sync-options {category_id?} {attribute_id?}';

    protected array $commonAttributes = [
        85, // Бренд
        88, // Серии
        4389, // Страна-изготовитель
//        6383, // Материал
        9461, // Коммерческий тип
        9782, // Класс опасности товара
        10096, // Цвет товара
        10400, // Гарантия
    ];

    public function handle()
    {
        $categoryId = $this->argument('category_id');
        $attributeId = $this->argument('attribute_id');

        $startTime = now();

        if (isset($categoryId) && isset($attributeId)) {
            $this->syncAttributeOptions($categoryId, $attributeId);
        }

        if (isset($categoryId) && empty($attributeId)) {
            $ozonCategory = OzonCategory::find($categoryId);
            foreach ($ozonCategory->attributes()->where('dictionary_id', '>', 0)->get() as $ozonAttribute) {
                $this->syncAttributeOptions($ozonCategory->id, $ozonAttribute->id);
            }
        }

        if (empty($categoryId) && empty($attributeId)) {
            foreach (OzonCategory::where('last_node', 1)->get() as $ozonCategory) {
                foreach ($ozonCategory->attributes()->where('dictionary_id', '>', 0)->get() as $ozonAttribute) {
                    $this->syncAttributeOptions($ozonCategory->id, $ozonAttribute->id);
                }
            }
        }

        $endTime = now();

        return $this->success($startTime, $endTime);
    }


    protected function syncAttributeOptions($categoryId, $attributeId)
    {
        $this->saveNextOptions($categoryId, $attributeId);
    }

    protected function saveNextOptions($categoryId, $attributeId, $lastValueId = 0, $limit = 5000)
    {
        $json = $this->ozonApi->getCategoryAttributeValues($categoryId, $attributeId, $lastValueId, $limit);

        $ozonAttribute = OzonAttribute::find($attributeId);

        $dataArray = json_decode($json, true);

        if(isset($dataArray['result']) && count($dataArray['result']) > 0) {

            DB::table('ozon_attribute_options')->upsert(
                $dataArray['result'], 'id'
            );

            foreach ($dataArray['result'] as $value) {

                DB::table('ozon_category_attribute_option')->updateOrInsert([
                    'ozon_category_id' => in_array($attributeId, $this->commonAttributes) ?  0 : $categoryId,
                    'ozon_attribute_id' => $ozonAttribute->id,
                    'ozon_attribute_option_id' => $value['id'],
                ]);
            }

            $lastValueId = last($dataArray['result'])['id'];

            if(isset($dataArray['has_next']) && $dataArray['has_next'] == true) {
                $this->saveNextOptions($categoryId, $attributeId, $lastValueId, $limit);
            }
        }
    }
}
