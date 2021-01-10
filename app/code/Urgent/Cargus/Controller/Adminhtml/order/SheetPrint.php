<?php

namespace Urgent\Cargus\Controller\Adminhtml\order;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Urgent\Cargus\Model\UrgentCargus;

/**
 * Class Index
 */
class SheetPrint extends Action implements CsrfAwareActionInterface
{
    /**
     * Index constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(Context $context)
    {
        parent::__construct($context);
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
        $id = $this->getRequest()->getParam('id');

        if ($id) {

            $urgentCargus = new UrgentCargus();
            $print = $urgentCargus->printSheet($id);

            header('Content-type:application/pdf');
            echo base64_decode($print);
            die();
        }
        $this->_redirect('cargus/order/index');
    }
}