<?php

/**
 * Product:       Xtento_ProductExport
 * ID:            MLJStlT2lyzljl8J0VCGgVGv2iLUq/Bs8N1L/Qdk6Bw=
 * Last Modified: 2016-04-14T15:37:57+00:00
 * File:          app/code/Xtento/ProductExport/Logger/Handler.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */
namespace Xtento\ProductExport\Logger;

class Handler extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * Logging level
     * @var int
     */
    protected $loggerType = Logger::INFO;

    /**
     * File name
     * @var string
     */
    protected $fileName = '/var/log/xtento_productexport.log';
}