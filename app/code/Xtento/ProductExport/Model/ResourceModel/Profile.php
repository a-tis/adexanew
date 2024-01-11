<?php

/**
 * Product:       Xtento_ProductExport
 * ID:            MLJStlT2lyzljl8J0VCGgVGv2iLUq/Bs8N1L/Qdk6Bw=
 * Last Modified: 2016-04-14T15:37:57+00:00
 * File:          app/code/Xtento/ProductExport/Model/ResourceModel/Profile.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\ProductExport\Model\ResourceModel;

class Profile extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('xtento_productexport_profile', 'profile_id');
    }
}
