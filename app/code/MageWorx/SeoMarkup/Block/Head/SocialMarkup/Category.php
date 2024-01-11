<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SeoMarkup\Block\Head\SocialMarkup;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;

class Category extends \MageWorx\SeoMarkup\Block\Head\SocialMarkup
{
    /**
     * @var \MageWorx\SeoMarkup\Helper\Category
     */
    protected $helperCategory;

    /**
     * @var \MageWorx\SeoMarkup\Helper\Website
     */
    protected $helperWebsite;

    /**
     * Category constructor.
     *
     * @param \MageWorx\SeoMarkup\Helper\Category $helperCategory
     * @param \MageWorx\SeoMarkup\Helper\Website $helperWebsite
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \MageWorx\SeoMarkup\Helper\Category $helperCategory,
        \MageWorx\SeoMarkup\Helper\Website $helperWebsite,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data
    ) {
        $this->helperCategory = $helperCategory;
        parent::__construct($registry, $helperWebsite, $context, $data);
    }

    /**
     * @return string
     */
    public function getPreparedUrl(): string
    {
        list($urlRaw) = explode('?', $this->_urlBuilder->getCurrentUrl());

        return rtrim($urlRaw, '/');
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getMarkupHtml(): string
    {
        if (!$this->helperCategory->isOgEnabled() && !$this->isTwEnabled()) {
            return '';
        }

        return $this->getSocialCategoryInfo();
    }

    /**
     * @return bool
     */
    protected function isTwEnabled(): bool
    {
        return $this->helperCategory->isTwEnabled() && $this->helperCategory->getTwUsername();
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getSocialCategoryInfo(): string
    {
        $html = '';

        $title       = $this->escapeHtml($this->pageConfig->getTitle()->get());
        $description = $this->escapeHtml($this->pageConfig->getDescription());
        $imageUrl    = $this->getCategoryImageUrl();

        if ($this->helperCategory->isOgEnabled()) {

            $type     = 'product.group';
            $siteName = $this->escapeHtml($this->helperWebsite->getName());

            $html = "\n<meta property=\"og:type\" content=\"" . $type . "\"/>\n";
            $html .= "<meta property=\"og:title\" content=\"" . $title . "\"/>\n";
            $html .= "<meta property=\"og:description\" content=\"" . $description . "\"/>\n";
            $html .= "<meta property=\"og:url\" content=\"" . $this->getPreparedUrl() . "\"/>\n";
            if ($siteName) {
                $html .= "<meta property=\"og:site_name\" content=\"" . $siteName . "\"/>\n";
            }

            if ($imageUrl) {
                $imageData = ['url' => $imageUrl];

                if ($this->getCategoryImageSize()) {
                    $imageData = array_merge($imageData, $this->getCategoryImageSize());
                }
            } else {
                $imageData = $this->getOgImageData();
            }

            if (isset($imageData['url'])) {
                $html .= "<meta property=\"og:image\" content=\"" . $imageData['url'] . "\"/>\n";

                if (isset($imageData['width'])) {
                    $html .= "<meta property=\"og:image:width\" content=\"" . $imageData['width'] . "\"/>\n";
                    $html .= "<meta property=\"og:image:height\" content=\"" . $imageData['height'] . "\"/>\n";
                }
            }

            if ($appId = $this->helperWebsite->getFacebookAppId()) {
                $html .= "<meta property=\"fb:app_id\" content=\"" . $appId . "\"/>\n";
            }
        }

        if ($this->isTwEnabled()) {
            $html = $html ? $html : "\n";
            $html .= "<meta name=\"twitter:card\" content=\"summary\"/>\n";
            $html .= "<meta name=\"twitter:site\" content=\"" . $this->helperCategory->getTwUsername() . "\"/>\n";
            $html .= "<meta name=\"twitter:title\" content=\"" . $title . "\"/>\n";
            $html .= "<meta name=\"twitter:description\" content=\"" . $description . "\"/>\n";

            if ($imageUrl) {
                $html .= "<meta name=\"twitter:image\" content=\"" . $imageUrl . "\"/>\n";
            }
        }

        return $html;
    }

    /**
     * @return string|null
     * @throws NoSuchEntityException
     */
    protected function getCategoryImageUrl()
    {
        $category = $this->registry->registry('current_category');
        $imageUrl = $category->getImageUrl();

        if (!$imageUrl || !is_string($imageUrl)) {
            return null;
        }

        $isRelativeUrl = substr($imageUrl, 0, 1) === '/';

        if ($isRelativeUrl) {
            $imageUrl = ltrim($imageUrl, '/');
            $imageUrl = $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_WEB) . $imageUrl;
        }

        return $imageUrl;
    }

    /**
     * @return array|bool
     */
    protected function getCategoryImageSize()
    {
        $category = $this->registry->registry('current_category');
        $image    = $category->getData('image');

        if ($image && is_string($image)) {
            $mediaDir      = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA);
            $isRelativeUrl = substr($image, 0, 1) === '/';

            if ($isRelativeUrl) {
                if (strpos($image, '/' . DirectoryList::MEDIA . '/') === 0) {
                    $filePath = substr_replace($image, '', 0, strlen('/' . DirectoryList::MEDIA . '/'));
                } else {
                    return false;
                }
            } else {
                $filePath = 'catalog/category/' . $image;
            }

            if ($mediaDir->isFile($filePath)) {
                $absolutePath = $mediaDir->getAbsolutePath($filePath);
                $imageAttr    = getimagesize($absolutePath);

                return [
                    'width'  => $imageAttr[0],
                    'height' => $imageAttr[1]
                ];
            }
        }

        return false;
    }
}
