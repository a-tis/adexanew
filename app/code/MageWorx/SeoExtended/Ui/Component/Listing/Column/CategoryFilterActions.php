<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SeoExtended\Ui\Component\Listing\Column;

use MageWorx\SeoAll\Helper\MagentoVersion;

class CategoryFilterActions extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * Url path  to edit
     *
     * @var string
     */
    const URL_PATH_EDIT = 'mageworx_seoextended/categoryfilter/edit';

    /**
     * Url path  to delete
     *
     * @var string
     */
    const URL_PATH_DELETE = 'mageworx_seoextended/categoryfilter/delete';

    /**
     * URL builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var MagentoVersion
     */
    protected $helperVersion;

    /**
     * CategoryFilterActions constructor.
     *
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param MagentoVersion $helperVersion
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        MagentoVersion $helperVersion,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder    = $urlBuilder;
        $this->helperVersion = $helperVersion;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }


    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $confirm = [
                'title'   => __('Delete "${ $.$data.name }"'),
                'message' => __(
                    'Are you sure you want to delete the SEO Category Filter "${ $.$data.name }" ?'
                )
            ];

            if ($this->helperVersion->checkModuleVersion('Magento_Ui', '101.1.4')) {
                $confirm['__disableTmpl'] = ['title' => false, 'message' => false];
            }

            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['id'])) {
                    $item[$this->getData('name')] = [
                        'edit'   => [
                            'href'  => $this->urlBuilder->getUrl(
                                static::URL_PATH_EDIT,
                                [
                                    'id' => $item['id']
                                ]
                            ),
                            'label' => __('Edit')
                        ],
                        'delete' => [
                            'href'    => $this->urlBuilder->getUrl(
                                static::URL_PATH_DELETE,
                                [
                                    'id' => $item['id']
                                ]
                            ),
                            'label'   => __('Delete'),
                            'confirm' => $confirm
                        ]
                    ];
                }
            }
        }

        return $dataSource;
    }
}
