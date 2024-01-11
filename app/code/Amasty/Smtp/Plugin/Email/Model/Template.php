<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Smtp
 */


namespace Amasty\Smtp\Plugin\Email\Model;

use Amasty\Smtp\Model\Config;
use Magento\Email\Model\Template as EmailTemplate;
use Magento\Framework\Registry;

class Template
{
    const TEMPLATE_STORE_ID_REGISTRY_KEY = 'amsmtp_template_store_id';

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param Registry $registry
     * @param Config $config
     */
    public function __construct(
        Registry $registry,
        Config $config
    ) {
        $this->registry = $registry;
        $this->config = $config;
    }

    public function beforeSetOptions(EmailTemplate $subject, $options)
    {
        if (isset($options['store']) && $this->config->isSmtpEnable($options['store'])) {
            $this->registry->register(self::TEMPLATE_STORE_ID_REGISTRY_KEY, $options['store'], true);
        }
    }
}
