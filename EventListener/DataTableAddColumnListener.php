<?php

namespace ProductStatus\EventListener;

use EasyProductManager\Events\DataTableAddQueryColumn;
use EasyProductManager\Events\DataTableColumnData;
use ProductStatus\Model\Map\ProductProductStatusTableMap;
use ProductStatus\Model\ProductProductStatus;
use Propel\Runtime\ActiveQuery\Criteria;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Model\Map\ProductSaleElementsTableMap;
use Thelia\Model\Product;
use Thelia\Model\ProductQuery;
use Thelia\Model\ProductSaleElementsQuery;

class DataTableAddColumnListener implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return [
            DataTableAddQueryColumn::PRODUCT_DATATABLE_ADD_QUERY_COLUMN => ['onColumnAdded',64],
            DataTableColumnData::PRODUCT_DATATABLE_COLUMN_ADD_DATA => ['onColumnDataAdded',64]
        ];
    }

    public function onColumnAdded(DataTableAddQueryColumn $event){
        $event->addColumn(['title'=>'Statut','orderable'=>false]);

        /** @var ProductQuery $query */
        $query = $event->getQuery();
        $query->useProductProductStatusQuery('product_product_status', Criteria::LEFT_JOIN)
            ->useProductStatusQuery('product_status',Criteria::LEFT_JOIN)
            ->withColumn('product_status.CODE','code')
            ->endUse()
            ->endUse()
        ;
    }

    public function onColumnDataAdded(DataTableColumnData $event){
        /** @var Product $product */
        $product = $event->getObject();
        $event->addDataTableJson($product->getId(),$product->getVirtualColumn('code')??'Normal');
    }
}