<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */
class Attachment_IndexController extends Zend_Controller_Action
{

    protected $_isPopup = false;

    public function init()
    {
        $this->view->isPopup = $this->_request->has('poplay');
        $this->_isPopup = $this->_request->has('poplay');

    }

    public function listAction()
    {
        $attachMapper = AttachmentMapper::getMasterInstance();

        $searchForm = new Form_Search();

        $limit = 20;

        if ($searchForm->isValidPartial($this->_request->getParams())) {
            $this->view->attPager = $attachMapper->getListPager($searchForm->getValues(), $this->_getParam('page', 1), $limit);
        }

        $searchForm->getElement('page')->setValue(1);
        $searchForm->setAction('/attachment/index/list');
        $this->view->searchForm = $searchForm;

    }

    public function chooseSingleAction ()
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

        $type = strtoupper('' . $this->getParam('type'));
        
        if (!empty($type) && in_array($type, AttachmentMapper::$types)) {
            $typeEl = $searchForm->getElement('att_type');
            $searchForm->removeElement('att_type');

            $searchForm->addElement('hidden', 'att_type');
            $searchForm->getElement('att_type')->setValidators($typeEl->getValidators())->setFilters($typeEl->getFilters());

            $params['att_type'] = $type;
        }

        if ($searchForm->isValidPartial($params)) {
            $this->view->attPager = $attachMapper->getListPager($searchForm->getValues(), $this->_getParam('page', 1), $limit);
        }

        $searchForm->getElement('page')->setValue(1);
        $searchForm->setAction($this->_request->getPathInfo());
        $this->view->searchForm = $searchForm;

    }

    public function appandItemsToParentAction()
    {
        $attachMapper = AttachmentMapper::getMasterInstance();

        $parent = $this->_getParam('parent');
        $parentId = (int)$this->_getParam('parentId');
        $parentGroup = $this->_getParam('parentGroup', AttachmentParentRelMapper::PARENT_GROUP_DEFAULT);

        if ($this->_request->isPost()) {
            if ($attsIds = $this->_getParam('attId')) {
                foreach ($attsIds as $attId) {
                    $attId = (int)$attId;
                    if (!empty($attId)) {
                        $attach = new Attachment( array('att_id' => $attId));
                        AttachmentParentRelMapper::getMasterInstance()->addRelation($attach, $parent, $parentId, $parentGroup);
                    }
                }
            }
            $this->_helper->json(array(
                'result' => 'ok',
                'arr' => $attsIds
            ));
            exit ;
        }

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
        $searchForm->setAction('/attachment/index/appand.items.to.parent');
        $this->view->searchForm = $searchForm;

        if ($this->_request->isXmlHttpRequest()) {
            $this->view->pager = $this->view->attPager;
            $this->view->noActions = true;
            $this->render('inc/att-list-for-pager');
            return;
        }

    }

    public function unsetFromParentAction()
    {
        $parent = $this->_getParam('parent');
        $parentId = (int)$this->_getParam('parentId');
        $parentGroup = $this->_getParam('parentGroup', AttachmentParentRelMapper::PARENT_GROUP_DEFAULT);
        $id = (int)$this->_getParam('id');

        AttachmentParentRelMapper::getMasterInstance()->unsetRelation($id, $parent, $parentId, $parentGroup);

        $this->_helper->json(array('result' => 'ok'));
        exit ;
    }

    public function deleteAction()
    {
        $id = (int)$this->_getParam('id');

        AttachmentMapper::getMasterInstance()->delete($id);

        $this->_helper->json(array('result' => 'ok'));
        exit ;
    }

    public function xUpdateDescriptionAction()
    {

        $attId = (int)$this->_getParam('id');

        $res = array('result' => 'err');
        if ($attId && $this->_request->isPost()) {
            
            $attachMapper = AttachmentMapper::getMasterInstance();
            $sAttach = new Attachment();
            $sAttach->id = $attId;
            $sAttach->description = trim(strip_tags($this->_getParam('description')));
            if($attachMapper->save($sAttach)) {
                $res = array('result' => 'ok');
            }
        }
        
        $this->_helper->json($res);
        exit;
        
    }

    public function editAction()
    {

        $attId = (int)$this->_getParam('id');
        $attachMapper = AttachmentMapper::getMasterInstance();

        $attach = $attachMapper->getById($attId);
        $form = null;
        if ($attach->isImage()) {
            $form = new Form_Image();
        } else {
            $form = new Form_File();
        }
        $form->setAction($this->view->url());
        
        if ($this->_isPopup) {
            $form->getElement('return')->setLabel('attachment_button_close_window')->setAttrib('id', 'jClose');
        }

        if ($this->_request->isPost()) {
            if ($form->isValid($_POST)) {

                $sAttach = new Attachment();
                $sAttach->id = $attach->id;
                $sAttach->description = $form->att_description->getValue();

                if ($form->uploader_att_url->isUploaded()) {

                    $fileInfo = $form->uploader_att_url->getFileInfo();
                    $fileInfo = $fileInfo['uploader_att_url'];

                    $sAttach->name = $form->uploader_att_url->getValue();
                    $sAttach->mime_type = $form->uploader_att_url->getMimeType();
                    $sAttach->filesize = $fileInfo['size'];

                    $imsize = getimagesize($form->uploader_att_url->getFileName());
                    if (!empty($imsize)) {
                        $sAttach->file_width = $imsize[0];
                        $sAttach->file_height = $imsize[1];
                    }

                    $sAttach->url = Agileo_Mds_Uploader::moveFormFileForObject($form->uploader_att_url, $sAttach);

                }
                
                if(isset($form->att_url_crop)) {
                    $sAttach->url_crop = $form->att_url_crop->getValue();
                }


                $attachmentForm = new AttachmentForm();
                // validate data
                if ($attachmentForm->isValidPartial($sAttach->toArray())) {
                    // save data
                    $attachMapper->save($sAttach);
                    $this->_redirect($this->_request->getPathInfo());

                } else {
                    throw new Agileo_Exception($attachmentForm->getErrors());
                }

            } else {
                throw new Agileo_Exception($form->getErrors());
            }
        }

        $form->populate($attach->toArray());
        $this->view->form = $form;

    }

    public function uploadSingleAction()
    {
        
        $form = new Form_File();
        $form->setAction($this->view->url());
        $form->removeElement('att_url');
        
        if ($this->_isPopup) {
            $form->removeElement('return');
        }
        if($this->_getParam('redirect')) {
            $form->addElement('hidden', 'redirect');
            $form->getElement('redirect')->setValue($this->_getParam('redirect'));
        }
        
        $this->view->target = $target = $this->_getParam('target');
        $this->view->type = $type = $this->_getParam('type');
        
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

                        $attach->type = AttachmentMapper::getTypeForMimeType($attach->mime_type);
                        $attach->status = AttachmentMapper::STATUS_ACTIVE;
                        $attach->description = $form->att_description->getValue();
                        $attach->create_date = date('Y-m-d H:i:s');

                        $attach->url = Agileo_Mds_Uploader::moveFormFileForObject($form->uploader_att_url, $attach);

                        $attachmentForm = new AttachmentForm();
                        // validate data
                        if ($attachmentForm->isValidPartial($attach->toArray())) {
                            // save data
                            $attachMapper->save($attach);
                        } else {
                            throw new Agileo_Exception($attachmentForm->getErrors());
                        }
                    } else {
                        throw new Agileo_Exception('Attachment save error');
                    }
                    
                    $target = $this->_getParam('target');
                    $poplay = $this->_getParam('poplay');
                    
                    if($target) {
                        $html = '';
                        echo '<script>parent.attachFieldCallback("'.$target.'", {id: "'.$attach->id.'", url: "'.$attach->url.'"' .($attach->isImage() ? ', thumb: "'.$this->view->mdsImage($attach->url, array('resize' => '240x240')).'"' : ''). '});</script>';
                        exit; 
                    }
                    
                    if($this->_getParam('redirect')) {
                        return $this->_redirect(base64_decode($this->_getParam('redirect')) . '/id/' . $attach->id. ($poplay ? '/poplay/' . $poplay : ''));
                    } else {
                        return $this->_redirect('/attachment/index/edit/id/' . $attach->id. ($target ? '/target/' . $target : '') . ($poplay ? '/poplay/' . $poplay : ''));
                    }

                }
            } else {
                throw new Agileo_Exception($form->getErrors());
            }
        }

        $this->view->form = $form;

    }

    public function uploaderAction()
    {
        $this->view->parent = $parent = $this->_getParam('parent', '');
        $this->view->parentId = $parentId = (int)$this->_getParam('parentId', 0);
        $this->view->parentGroup = $parentGroup = $this->_getParam('parentGroup', AttachmentParentRelMapper::PARENT_GROUP_DEFAULT);
        $this->view->type = $type = $this->_getParam('type');

        Zend_Registry::get('log')->debug(__METHOD__ . ': ' . print_r($_REQUEST, true));

        if ($this->_request->isPost() && $this->_request->has('add')) {
            $descs = $this->_request->getParam('desc');
            foreach ($this->_request->getParam('add') as $attId => $v) {
                $attach = new Attachment();
                $attach->id = $attId;
                $attach->description = !empty($descs[$attId]) ? $descs[$attId] : '';
                $attach->status = AttachmentMapper::STATUS_ACTIVE;

                $attachmentForm = new AttachmentForm();
                // validate data
                if ($attachmentForm->isValidPartial($attach->toArray())) {
                    // save data
                    AttachmentMapper::getMasterInstance()->save($attach);

                    if (!empty($parent) && !empty($parentId)) {
                        // add relation
                        AttachmentParentRelMapper::getMasterInstance()->addRelation($attach, $parent, $parentId, $parentGroup);
                    }

                } else {
                    throw new Agileo_Exception($attachmentForm->getErrors());
                }

            }

            if ($this->_request->isXmlHttpRequest()) {
                $res = array(
                    'result' => 'ok',
                    'parent' => $parent,
                    'parentId' => $parentId,
                    'parentGroup' => $parentGroup
                );
                if (!empty($attach)) {
                    $attach = AttachmentMapper::getMasterInstance()->getById($attach->id);
                    $res = array_merge($res, $attach->toArray());
                }
                $this->_helper->json($res);
                exit ;
            } elseif ($hits->_isPopup) {
                return $this->_redirect('/attachment/index/list/poplay/1');
            } else {
                return $this->_redirect('/attachment/index/list');
            }

        }

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
