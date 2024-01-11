<?php

/**
 * Grid Admin Cagegory Map Record Save Controller.
 * @category  Webkul
 * @package   Webkul_Grid
 * @author    Webkul
 * @copyright Copyright (c) 2010-2016 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Grid\Controller\Adminhtml\Grid;


use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Webkul\Grid\Model\GridFactory
     */
    var $gridFactory;
    protected $_mediaDirectory;
    protected $_fileUploaderFactory;


    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Webkul\Grid\Model\GridFactory $gridFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Webkul\Grid\Model\GridFactory $gridFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactorySecond
    ) {
        parent::__construct($context);
        $this->gridFactory = $gridFactory;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_fileUploaderFactorySecond = $fileUploaderFactorySecond;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        /*For Image Upload*/

        $data = $this->getRequest()->getPostValue();
        //var_dump($data);
        //exit;
        //print_r($data['prod_img']);
        if(isset($_FILES['selection']['name']) && $_FILES['selection']['name'] != '') {
            //try{

            $target = $this->_mediaDirectory->getAbsolutePath('imagefolder/');

            $targetOne = "imagefolder/";
            /** @var $uploader \Magento\MediaStorage\Model\File\Uploader */
            $uploader = $this->_fileUploaderFactory->create(['fileId' => 'selection']);
            /** Allowed extension types */
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png', 'zip', 'doc']);
            /** rename file name if already exists */
            $uploader->setAllowRenameFiles(true);
            /** upload file in folder "mycustomfolder" */
            $result = $uploader->save($target);
            /*If file found then display message*/
            //print_r($result);
            //exit;


            /*}
            catch (Exception $e)
            {
                $this->messageManager->addError($e->getMessage());
            }*/
            $data['selection'] = $targetOne . $result['file'];
        }else{
            $data['selection'] = $data['selection']['value'];
        }
        //exit;


        if(isset($_FILES['prod_img']['name']) && $_FILES['prod_img']['name'] != '') {
            //try{

            $targetSecond = $this->_mediaDirectory->getAbsolutePath('imagefolder/');

            $targetOneSecond = "imagefolder/";
            /** @var $uploaderSecond \Magento\MediaStorage\Model\File\Uploader */
            $uploaderSecond = $this->_fileUploaderFactorySecond->create(
                ['fileId' => 'prod_img']
            );
            /** Allowed extension types */
            $uploaderSecond->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png', 'zip', 'doc']);
            /** rename file name if already exists */
            $uploaderSecond->setAllowRenameFiles(true);
            /** upload file in folder "mycustomfolder" */
            $resultSecond = $uploaderSecond->save($targetSecond);
            /*If file found then display message*/
            //print_r($result);


            /*}
            catch (Exception $e)
            {
                $this->messageManager->addError($e->getMessage());
            }*/


            $data['prod_img'] = $targetOneSecond . $resultSecond['file'];
        }else{
            $data['prod_img'] = $data['prod_img']['value'];
        }
        //print_r($data['prod_img']);
        //exit;

        if (!$data) {
            $this->_redirect('grid/grid/addrow');
            return;
        }
        try {
            $rowData = $this->gridFactory->create();
            $rowData->setData($data);
            if (isset($data['id'])) {
                $rowData->setEntityId($data['id']);
            }
            $rowData->save();
            $this->messageManager->addSuccess(__('Row data has been successfully saved.'));
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
        $this->_redirect('grid/grid/index');
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Grid::save');
    }
}
