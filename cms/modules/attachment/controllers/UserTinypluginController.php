<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Attachment_UserTinypluginController extends Zend_Controller_Action
{

    public function init()
    {
        parent::init();
        
        Zend_Layout::getMvcInstance()->setLayout('tinypopup');
        
    }

    public function videoAction()
    {
        $this->view->type = $type = AttachmentMapper::TYPE_VIDEO;
        
        if($attId = $this->_getParam('id')) {
            
            $attachmentMapper = AttachmentMapper::getMasterInstance();
            
            if($attach = $attachmentMapper->getById($attId)) {
                $this->view->attach = $attach;
            }
        }
        
    }

    public function videoChooseAction()
    {
        $attachMapper = AttachmentMapper::getMasterInstance();

        $limit = 20;
        $params = $this->_request->getParams();
        
        //$params['att_status'] = AttachmentMapper::STATUS_ACTIVE;
        
        $searchForm = new Form_Search();
        $statusEl = $searchForm->getElement('att_status');
        $searchForm->removeElement('att_status');
        $searchForm->addElement('hidden', 'att_status');
        $searchForm->getElement('att_status')->setValidators($statusEl->getValidators())->setFilters($statusEl->getFilters());
        
        $params['att_type'] = AttachmentMapper::TYPE_VIDEO;
        $typeEl = $searchForm->getElement('att_type');
        $searchForm->removeElement('att_type');
        $searchForm->addElement('hidden', 'att_type');
        $searchForm->getElement('att_type')->setValidators($typeEl->getValidators())->setFilters($typeEl->getFilters());

        if ($searchForm->isValidPartial($params)) {
            $this->view->attPager = $attachMapper->getListPager($searchForm->getValues(), $this->_getParam('page', 1), $limit);
        }

        $searchForm->getElement('page')->setValue(1);
        $searchForm->setAction($this->_request->getPathInfo());
        $this->view->searchForm = $searchForm;
        
    }
    public function videoSaveAction()
    {
        $attId = (int) $this->_getParam('att_id');
        
        $res = array('id' => $attId, 'result' => 'err');
        
        if ($this->_request->isPost()) {
            
            $attachmentMapper = AttachmentMapper::getMasterInstance();
            
            if($youtube = $this->_getParam('youtube', '')) {
                
                $attach = $attachmentMapper->cms_youtubeSaver($youtube, Zend_Auth::getInstance()->getIdentity()->id, $this->_getParam('att_description', ''));
                
                if(is_int($attach)) {
                    $res['youtube-status'] = $attach;
                }
                
            } elseif(!empty($attId)) {
                
                $attach = $attachmentMapper->getById($attId);

                // sprawdzamy czy nie mamy do czynienia z załącznikiem użytkownika jezeli tak tworzymy kopię... 
                if(!empty($attach->usr_id)) {
                    $copyAttach = $attachmentMapper->createCopyForAdmin($attach, Zend_Auth::getInstance()->getIdentity()->id);
                    $attId = $copyAttach->id;
                    $attach = $copyAttach; 
                }
                
                $data = array(
                        'att_id' => $attId,
                        'att_description' => $this->_getParam('att_description', '')
                );
                $attachment = new Attachment($data);
                $attachmentMapper->save($attachment);
                
            }

            if(!empty($attach) && $attach instanceof Attachment && !empty($attach->id)) {
                
                $res['id'] = $attach->id;
                $attach = $attachmentMapper->getById($attach->id);
                
                if($attach->isSourceYoutube()) {
                    $res['imageThumb'] = $attach->thumb;
                    $res['youtube'] = $attach->url;
                    $res['embed'] = $this->view->mdsVideo($attach, array('width' => 320, 'height' => 180));
                    $res['result'] = 'ok';
                } else {
                    $res['noSupported'] = 1;
                }
                
            }
            
        }

        $this->_helper->json($res);
        exit;
        
    }


    public function imageAction()
    {
        $this->view->type = $type = AttachmentMapper::TYPE_IMAGE;
        
        $this->view->align = $this->_getParam('align', '');
        
        if($attId = $this->_getParam('id')) {
            
            $attachmentMapper = AttachmentMapper::getMasterInstance();
            
            if($attach = $attachmentMapper->getById($attId)) {
                $this->view->attach = $attach;
                $this->view->gallery = $attachmentMapper->getListForParent('Attachment', $attach->id);
                
            }
        }
        
    }
    
    public function imagesChooseAction()
    {
            
        $attachMapper = AttachmentMapper::getMasterInstance();

        if ($this->_request->isPost()) {
            $data = array();
            if ($attsIds = $this->_getParam('attId')) {
                $list = $attachMapper->getByIds($attsIds);
                foreach($list as $attach) {
                    $item = array(
                        'id' => $attach->id,
                        'file' =>  $this->view->mdsAttachment($attach->url),
                        'id' => $attach->id,
                        'description' => $attach->description,
                    );
                    if ($attach->isImage()) {
                        $item['thumb'] = $this->view->mdsImage($attach->url, array('resize' => '100x100')); 
                        $item['thumb2x'] = $this->view->mdsImage($attach->url, array('resize' => '200x200')); 
                    }
                    $data[] = $item;
                }
            }
            $this->_helper->json(array(
                'result' => 'ok',
                'data' => $data
            ));
            exit ;
        }

        $limit = 20;
        $params = $this->_request->getParams();

        $searchForm = new Form_Search();
        
        $params['att_status'] = AttachmentMapper::STATUS_ACTIVE;
        $statusEl = $searchForm->getElement('att_status');
        $searchForm->removeElement('att_status');
        $searchForm->addElement('hidden', 'att_status');
        $searchForm->getElement('att_status')->setValidators($statusEl->getValidators())->setFilters($statusEl->getFilters());

        $params['att_type'] = AttachmentMapper::TYPE_IMAGE;
        $typeEl = $searchForm->getElement('att_type');
        $searchForm->removeElement('att_type');
        $searchForm->addElement('hidden', 'att_type');
        $searchForm->getElement('att_type')->setValidators($typeEl->getValidators())->setFilters($typeEl->getFilters());

        if ($searchForm->isValidPartial($params)) {
            $this->view->attPager = $attachMapper->getListPager($searchForm->getValues(), $this->_getParam('page', 1), $limit);
        }

        $searchForm->getElement('page')->setValue(1);
        $searchForm->setAction($this->_request->getPathInfo());
        $this->view->searchForm = $searchForm;
        
        if ($this->_request->isXmlHttpRequest()) {
            $this->view->pager = $this->view->attPager;
            $this->view->noActions = true;
            echo $this->view->render('index/inc/att-list-for-pager.phtml');
            exit;
        }

    }
    
    public function imageSaveAction()
    {
        $attId = (int) $this->_getParam('att_id');
        
        $res = array('id' => $attId, 'result' => 'err');
        
        if ($attId && $this->_request->isPost()) {
            
            $attachmentMapper = AttachmentMapper::getMasterInstance();
            $relationMapper = AttachmentParentRelMapper::getMasterInstance();
            
            // check owner
            $attach = $attachmentMapper->getById($attId);
            
            $descById = $this->_getParam('att_desc', array());
            
            // sprawdzamy czy nie mamy do czynienia z załącznikiem użytkownika jezeli tak tworzymy kopię i zapisujemy nowe relacje
            if(!empty($attach->usr_id)) {

                $startTransaction = $attachmentMapper->beginTransaction();
                try {
                    
                    $attach->description = $this->_getParam('att_description', '');
                    $copyAttach = $attachmentMapper->createCopyForAdmin($attach, Zend_Auth::getInstance()->getIdentity()->id);
                    
                    $parent = 'Attachment';
                    $parentId = $copyAttach->id;
            
                    $relIds = array_keys($this->_getParam('att_item', array()));
                    if(!empty($relIds)) {
                        $relAttachs = $attachmentMapper->getByIds($relIds);
                        foreach($relAttachs as $relAttach) {
                            
                            $relAttach->description = !empty($descById[$relAttach->id]) ? $descById[$relAttach->id] : '';
                            $relAttach->status = AttachmentMapper::STATUS_ACTIVE;
                            
                            if(!empty($relAttach->usr_id)) {
                                $relCopyAttach = $attachmentMapper->createCopyForAdmin($relAttach, Zend_Auth::getInstance()->getIdentity()->id);
                                $relationMapper->addRelation($relCopyAttach->id, $parent, $parentId);
                            } else {
                                $attachmentMapper->save($relAttach);
                                $relationMapper->addRelation($relAttach->id, $parent, $parentId);
                            }
                        }
                    }

                    $attId = $copyAttach->id;
                    $attach = $copyAttach;                     
                    
                    
                    
                    // $attachmentMapper->rollBack($startTransaction);
                    // exit;
                    $attachmentMapper->commit($startTransaction);
                    
                    $res['id'] = $copyAttach->id;
                    $res['result'] = 'ok';
                    
                    $res['imageThumb'] = $this->view->mdsImage($copyAttach->url, array('resize' => '200x200'));
                    $res['imageThumbCenter'] = $this->view->mdsImage($copyAttach->url, array('resize' => '480x360'));
                    $res['imageBig'] = $this->view->mdsImage($copyAttach->url, array('resize' => '800x600'));
  
                } catch (Exception $e) {
                    $attachmentMapper->rollBack($startTransaction);
                    throw $e;
                }
                
            } else {
                
                $data = array(
                    'att_id' => $attId,
                    'att_description' => $this->_getParam('att_description', ''),
                    'att_status' => AttachmentMapper::STATUS_ACTIVE
                );
                $attachment = new Attachment($data);
                
                $parent = 'Attachment';
                $parentId = $attId;
                
                $list = $attachmentMapper->getListForParent($parent, $parentId);
                $oldIds = $list->getAllEntityIds();
                $newIds = array_keys($this->_getParam('att_item', array()));
                
                $removeRelations = array_diff($oldIds, $newIds);
                $addRelations = array_diff($newIds, $oldIds);
    
                $startTransaction = $attachmentMapper->beginTransaction();
                try {
                    
                    $attachment->gallery_count = count($newIds) > 0 ? count($newIds)+1 : 0; 
                    $attachmentMapper->save($attachment);
                    
                    foreach($newIds as $uAttId) {
                        $data = array(
                            'att_id' => $uAttId,
                            'att_description' => !empty($descById[$uAttId]) ? $descById[$uAttId] : '',
                            'att_status' => AttachmentMapper::STATUS_ACTIVE
                        );
                        $uAttachment = new Attachment($data);
                        $attachmentMapper->save($uAttachment);
                    }
                    
                    foreach($removeRelations as $rAttId) {
                        $relationMapper->unsetRelation($rAttId, $parent, $parentId);
                    }
    
                    foreach($addRelations as $rAttId) {
                        $relationMapper->addRelation($rAttId, $parent, $parentId);
                    }
                    
                    $attachmentMapper->commit($startTransaction);
                    
                    $res['result'] = 'ok';
                    
                    $res['imageThumb'] = $this->view->mdsImage($attach->url, array('resize' => '200x200'));
                    $res['imageThumbCenter'] = $this->view->mdsImage($attach->url, array('resize' => '480x360'));
                    $res['imageBig'] = $this->view->mdsImage($attach->url, array('resize' => '800x600'));
    
                } catch (Exception $e) {
                    $attachmentMapper->rollBack($startTransaction);
                    throw $e;
                }
                
            }

        }

        $this->_helper->json($res);
        exit;
        
    }



    public function fileAction()
    {
        $this->view->type = $type = AttachmentMapper::TYPE_FILE;
        
        if($attId = $this->_getParam('id')) {
            
            $attachmentMapper = AttachmentMapper::getMasterInstance();
            
            if($attach = $attachmentMapper->getById($attId)) {
                $this->view->attach = $attach;
            }
        }
        
    }
    
    public function fileChooseAction()
    {
            
        $attachMapper = AttachmentMapper::getMasterInstance();

        $this->view->target = $target = $this->_getParam('target');

        $limit = 20;
        $params = $this->_request->getParams();
        $params['att_status'] = AttachmentMapper::STATUS_ACTIVE;

        $searchForm = new Form_Search();
        $statusEl = $searchForm->getElement('att_status');
        $searchForm->removeElement('att_status');

        $searchForm->addElement('hidden', 'att_status');
        $searchForm->getElement('att_status')->setValidators($statusEl->getValidators())->setFilters($statusEl->getFilters());

        $type = $this->getParam('type');
        
        if (!empty($type) && in_array($type, AttachmentMapper::$types)) {
            $typeEl = $searchForm->getElement('att_type');
            $searchForm->removeElement('att_type');

            $searchForm->addElement('hidden', 'att_type');
            $searchForm->getElement('att_type')->setValidators($typeEl->getValidators())->setFilters($typeEl->getFilters());

            $params['att_type'] = strtoupper('' . $type);
        }

        if ($searchForm->isValidPartial($params)) {
            $this->view->attPager = $attachMapper->getListPager($searchForm->getValues(), $this->_getParam('page', 1), $limit);
        }

        $searchForm->getElement('page')->setValue(1);
        $searchForm->setAction($this->_request->getPathInfo());
        $this->view->searchForm = $searchForm;
        
    }
    
    public function fileSaveAction()
    {
        $attId = (int) $this->_getParam('att_id');
        
        $res = array('id' => $attId, 'result' => 'err');
        
        if ($attId && $this->_request->isPost()) {
            
            $attachmentMapper = AttachmentMapper::getMasterInstance();
            $relationMapper = AttachmentParentRelMapper::getMasterInstance();
            
            // check owner
            if($attach = $attachmentMapper->getById($attId)) {

                // sprawdzamy czy nie mamy do czynienia z załącznikiem użytkownika jezeli tak tworzymy kopię i zapisujemy nowe relacje
                if(!empty($attach->usr_id)) {
    
                    $attach->description = $this->_getParam('att_description', '');
                    $copyAttach = $attachmentMapper->createCopyForAdmin($attach, Zend_Auth::getInstance()->getIdentity()->id);
     
                    $attId = $copyAttach->id;
                    $attach = $copyAttach;
                      
                } else {
                    
                    $data = array(
                        'att_id' => $attId,
                        'att_description' => $this->_getParam('att_description', ''),
                        'att_status' => AttachmentMapper::STATUS_ACTIVE
                    );
                    $attachment = new Attachment($data);
                    $attachmentMapper->save($attachment);
                    $attach->description = $attachment->description; 
                    
                }
                
                $res['result'] = 'ok';
                $res['id'] = $attach->id;
                $res['file'] = $this->view->mdsAttachment($attach->url);
                $res['description'] = $attach->description;
                
            }

        }

        $this->_helper->json($res);
        exit;
        
    }

    public function uploadFileAction()
    {

        $type = $this->_getParam('type');

        $form = new Form_File();

        $response = array();
        if ($this->_request->isPost()) {
            if ($form->isValid($_POST)) {
                if ($form->uploader_att_url->isUploaded()) {

                    $attach = new Attachment();
                    $attach->adm_id = Zend_Auth::getInstance()->getIdentity()->id;

                    $attachMapper = AttachmentMapper::getMasterInstance();

                    // create object
                    if ($attachMapper->save($attach)) {

                        $fileInfo = $form->uploader_att_url->getFileInfo();
                        $fileInfo = $fileInfo['uploader_att_url'];

                        $attach->name = $form->uploader_att_url->getValue();
                        $attach->mime_type = $form->uploader_att_url->getMimeType();
                        $attach->filesize = $fileInfo['size'];

                        $imsize = getimagesize($form->uploader_att_url->getFileName());
                        if (!empty($imsize)) {
                            $attach->file_width = $imsize[0];
                            $attach->file_height = $imsize[1];
                            $attach->mime_type = $imsize['mime'];
                        }
                        $attach->create_date = date('Y-m-d H:i:s');

                        $attach->type = AttachmentMapper::getTypeForMimeType($attach->mime_type);

                        $attach->url = Agileo_Mds_Uploader::moveFormFileForObject($form->uploader_att_url, $attach);

                        $attachmentForm = new AttachmentForm();
                        // validate data
                        if ($attachmentForm->isValidPartial($attach->toArray())) {
                            // save data
                            $attachMapper->save($attach);

                            $response['url'] = $attach->url;
                            $response['name'] = $attach->name;
                            if ($attach->isImage()) {
                                $response['thumb'] = $this->view->mdsImage($attach->url, array('resize' => '100x100'));
                                $response['thumb2x'] = $this->view->mdsImage($attach->url, array('resize' => '200x200'));
                            }
                            $response['file'] = $this->view->mdsAttachment($attach->url);
                            $response['id'] = $attach->id;

                        } else {
                            $response['error'] = 'Attachment data no valid';
                        }
                    } else {
                        $response['error'] = 'Attachment save error';
                    }

                }
            } else {
                $response['error'] = 'File data no valid';
            }
        }
        $this->_helper->json($response);
        exit ;
    }


}
