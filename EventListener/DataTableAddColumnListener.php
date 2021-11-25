<?php

namespace ProductStatus\EventListener;

use EasyProductManager\Events\DataTableAddColumn;
use EasyProductManager\Events\DataTableColumnData;
use ProductStatus\Model\Map\ProductProductStatusTableMap;
use ProductStatus\Model\ProductProductStatus;
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
        $event->addColumn(['title'=>'Statut','orderable'=>true]);
//        $event->addColumn(['title'=>'test','orderable'=>false]);
    }

    public function onColumnDataAdded(DataTableColumnData $event){
        /** @var ProductQuery $query */
        $query = $event->getQuery();
        $request = $event->getRequest();

        $products = $query->limit($this->getLength($request))->find();
//        $query->withColumn($event->getNewColumns()['status']['column'],$event->getNewColumns()['status']['label']);
//        $query->useProductCategoryQuery()
//            ->withColumn('product_category.POSITION', $event->getNewColumns()['status']['label'])
//            ->endUse();

        /** @var Product $product */
        foreach ($products as $product) {
//            // Jointure product statut
//            $statut = $product->getVirtualColumn('productStatus');
//
//            $query->useProductProductStatusQuery()
//                ->filterByProductId($product->getId())
//                ->useProductStatusQuery('productStatus')
//                ->endUse()
//                ->endUse()
//            ;
        }

        $event->setQuery($query);
//        $event->addDataTableJson($product->hasVirtualColumn('productStatus') ? $product->getVirtualColumn('productStatus') : 'aucun');
        $event->addDataTableJson($product->getVersion());
//        $event->addDataTableJson($product->getVirtual());
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