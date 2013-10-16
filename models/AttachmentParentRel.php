<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */
class AttachmentParentRel extends Agileo_Object
{
    protected $_subObjectsConfig = array(
        'Attachment' => 'att_id'
    );
}
