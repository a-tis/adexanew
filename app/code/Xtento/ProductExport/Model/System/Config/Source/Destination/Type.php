<?php

/**
 * Product:       Xtento_ProductExport
 * ID:            MLJStlT2lyzljl8J0VCGgVGv2iLUq/Bs8N1L/Qdk6Bw=
 * Last Modified: 2016-04-14T15:37:35+00:00
 * File:          app/code/Xtento/ProductExport/Model/System/Config/Source/Destination/Type.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\ProductExport\Model\System\Config\Source\Destination;

use Magento\Framework\Option\ArrayInterface;

/**
 * @codeCoverageIgnore
 */
class Type implements ArrayInterface
{
    /**
     * @var \Xtento\ProductExport\Model\Destination
     */
    protected $destinationModel;

    /**
     * @param \Xtento\ProductExport\Model\Destination $destinationModel
     */
    public function __construct(\Xtento\ProductExport\Model\Destination $destinationModel)
    {
        $this->destinationModel = $destinationModel;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return $this->destinationModel->getTypes();
    }

    public function getName($type) {
        foreach ($this->toOptionArray() as $optionType => $name) {
            if ($optionType == $type) {
                return $name;
            }
        }
        return '';
    }
}
