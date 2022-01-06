<?php


namespace ProductStatus\Controller;

use ProductStatus\Events\UpdateProductStatusEvent;
use ProductStatus\Form\EditProductStatusForm;
use ProductStatus\Model\ProductProductStatus;
use ProductStatus\Model\ProductProductStatusQuery;
use ProductStatus\Model\ProductStatusQuery;
use ProductStatus\ProductStatus;
use ProductStatus\Form\StatusContentForm;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Controller\Admin\ProductController;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Core\Translation\Translator;
use Thelia\Model\Base\ProductQuery;
use Thelia\Tools\URL;

class ProductStatusController extends ProductController
{
    public function updateStatus($productId = null, $statusId = null)
    {
        if (null !== $response = $this->checkAuth(AdminResources::PRODUCT, array(), AccessManager::UPDATE)) {
            return $response;
        }

        $message = null;

        try {
            if ($productId === null) {
                $productId = $this->getRequest()->get("product_id");
            }

            $product = ProductQuery::create()->findPk($productId);
            if ($statusId === null) {
                $statusId = $this->getRequest()->get("status_id");
            }
            $status = ProductStatusQuery::create()->findPk($statusId);

            if (null === $product) {
                throw new \InvalidArgumentException("The product you want to update status does not exist");
            }
            if (null === $status) {
                throw new \InvalidArgumentException("The status you want to set to the product does not exist");
            }

            $statusToEdit = ProductProductStatusQuery::create()
                ->findOneByProductId($productId);

            if(!$statusToEdit) {
                $newEntry = new ProductProductStatus();
                $statusToEdit = $newEntry->setProductId($productId);
            }

            $statusToEdit
                ->setProductStatusId($status->getId())
                ->save();

            $event = new UpdateProductStatusEvent();
            $event->setProduct($statusToEdit->getProduct());
            $event->setProductProductStatus($statusToEdit);
            $this->getDispatcher()->dispatch(UpdateProductStatusEvent::PRODUCT_STATUS_UPDATE, $event);

        } catch (\Exception $e) {
            $message = $e->getMessage();
        }

        $url = $_SERVER['HTTP_REFERER'];
        return $this->generateRedirect(URL::getInstance()->absoluteUrl($url, $message ? ['errorMessage' => $message] : null));
    }

}
