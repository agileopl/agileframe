<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    All rights reserved
 */
interface PublicationInterface
{
    public function isAvailable();
    public function areRequiredFields();
}
