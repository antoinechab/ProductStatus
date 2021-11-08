<?php

namespace ProductStatus\EventListener;

use EasyProductManager\Events\DataTableAddColumn;
use EasyProductManager\Events\DataTableColumnData;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DataTableAddColumnListener implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return [
            DataTableAddColumn::PRODUCT_DATATABLE_ADD_COLUMN => ['onColumnAdded',64],
            DataTableColumnData::PRODUCT_DATATABLE_COLUMN_ADD_DATA => ['onColumnDataAdded',64]
        ];
    }

    public function onColumnAdded(DataTableAddColumn $event){
        $data = $event->getData();
    }

    public function onColumnDataAdded(DataTableColumnData $event){

    }
}