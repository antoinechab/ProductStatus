<?php

namespace ProductStatus\EventListener;

use EasyProductManager\Events\DataTableAddColumn;
use EasyProductManager\Events\DataTableColumnData;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Model\Product;
use Thelia\Model\ProductQuery;

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
//        $event->addColumn(['title'=>'Statut','orderable'=>true]);
    }

    public function onColumnDataAdded(DataTableColumnData $event){
        /** @var ProductQuery $query */
        $query = $event->getQuery();
        $request = $event->getRequest();

        foreach ($event->getNewColumns() as $newColumn){
            $query->withColumn($newColumn[0],$newColumn[1]);

        }

        // colonne statut
        $products = $query->limit($this->getLength($request))->find();

        /** @var Product $product */
        foreach ($products as $product) {
//            // Jointure product statut
//            $query->useProductProductStatusQuery()
//                ->filterByProductId($product->getId())
//                ->useProductStatusQuery('productStatus')
//                ->endUse()
//                ->endUse()
//            ;
//            $statut = $product->getVirtualColumn('statut');
        }

        $event->setQuery($query);
    }

    /**
     * @param Request $request
     * @return int
     */
    protected function getLength(Request $request)
    {
        return (int) $request->get('length');
    }


}