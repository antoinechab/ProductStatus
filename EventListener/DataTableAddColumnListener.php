<?php

namespace ProductStatus\EventListener;

use EasyProductManager\Events\DataTableAddQueryColumn;
use EasyProductManager\Events\DataTableColumnData;
use ProductStatus\Form\StatusContentForm;
use ProductStatus\Model\Map\ProductProductStatusTableMap;
use ProductStatus\Model\ProductProductStatus;
use Propel\Runtime\ActiveQuery\Criteria;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Model\Map\ProductSaleElementsTableMap;
use Thelia\Model\Product;
use Thelia\Model\ProductQuery;
use Thelia\Model\ProductSaleElementsQuery;
use Thelia\Tools\URL;

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

        $form = [
            'name'=>StatusContentForm::getName(),
            'label'=>'statutForm',
            'data'=>[]
        ];
        $event->setValidationForm($form);
    }

    public function onColumnDataAdded(DataTableColumnData $event){
        /** @var Product $product */
        $product = $event->getObject();
        //$data = [data,href,render,dataRender,form_route];
        $data = [
            $product->getVirtualColumn('code')??'Normal',
            '',
            'list',
            [1=>'normal',2=>'discontinued',3=>'sale',4=>'oddment'],
            URL::getInstance()->absoluteUrl('/admin/module/ProductStatus/product/update/'.$product->getId().'/status')
        ];
        $event->addDataTableJson($product->getId(),$data);
    }
}