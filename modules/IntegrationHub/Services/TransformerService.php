<?php

namespace Modules\IntegrationHub\Services;

class TransformerService
{
    public function transform($data, $mapping)
    {
        $transformedData = [];
        foreach ($mapping as $sourceKey => $destinationKey) {
            if (isset($data[$sourceKey])) {
                $transformedData[$destinationKey] = $data[$sourceKey];
            }
        }
        return $transformedData;
    }
}
