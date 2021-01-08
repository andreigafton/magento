<?php

namespace Urgent\Cargus\Controller\Adminhtml\order;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Urgent\Cargus\Model\UrgentCargus;
use Exception;

/**
 * Class Index
 */
class ValidateDelete extends Action implements CsrfAwareActionInterface
{
    /**
     * @var ResourceConnection
     */
    private ResourceConnection $_resource;

    /**
     * Index constructor.
     *
     * @param Context $context
     * @param ResourceConnection $resource
     */
    public function __construct(Context $context, ResourceConnection $resource)
    {
        parent::__construct($context);
        $this->_resource = $resource;
    }

    public function createCsrfValidationException(RequestInterface $request): ? InvalidRequestException
    {
        return null;
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

    /**
     * Load the page defined in view/adminhtml/layout/order_index.xml
     *
     * @return Page
     */
    public function execute()
    {
        $awbs = $this->getRequest()->getParam('awb');

        if ($awbs) {
            $urgentCargus = new UrgentCargus();

            try {
                foreach ($awbs as $code) {
                    $urgentCargus->deleteAwb($code);
                }

                $connection = $this->_resource->getConnection(ResourceConnection::DEFAULT_CONNECTION);

                $query = "UPDATE `awb_expeditii` SET `status` = '0', `cod_bara` = '' WHERE `cod_bara` IN('".implode("','", $awbs)."')";
                $connection->query($query);

                $this->messageManager->addNoticeMessage(__('AWB-urile selectate au fost anulate!'));
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage((__($e->getMessage())));
            }
        }
        $this->_redirect('cargus/order/index');
    }
}