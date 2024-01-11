<?php
/**
 * Grid GridInterface.
 * @category  Webkul
 * @package   Webkul_Grid
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Grid\Api\Data;

interface GridInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case.
     */
    const ENTITY_ID = 'entity_id';
    const TITLE = 'title';
    const CONTENT = 'content';
    const PUBLISH_DATE = 'publish_date';
    const IS_ACTIVE = 'is_active';
    const UPDATE_TIME = 'update_time';
    const CREATED_AT = 'created_at';
    const SELECTION = 'selection';
    const BUTTON = 'button';
    const PRODSKU = 'prodsku';
    const BUTTON_URL = 'button_url';
    const PROD_IMG = 'prod_img';


    public function getButtonUrl();
    public function setButtonUrl($buttonurl);

    public function getProdImg();
    public function setProdImg($prodimg);
   /**
    * Get EntityId.
    *
    * @return int
    */
    public function getEntityId();

   /**
    * Set EntityId.
    */
    public function setEntityId($entityId);

   /**
    * Get Title.
    *
    * @return varchar
    */
    public function getTitle();

   /**
    * Set Title.
    */
    public function setTitle($title);

    /**
    * Get Button.
    *
    * @return varchar
    */
    public function getButton();

   /**
    * Set Button.
    */
    public function setButton($button);

    /**
    * Get Prodsku.
    *
    * @return varchar
    */
    public function getProdsku();

   /**
    * Set Prodsku.
    */
    public function setProdsku($prodsku);

    /**
     * Get Selection.
     *
     * @return varchar
     */
    public function getSelection();

    /**
     * Set Selection.
     */
    public function setSelection($title);

   /**
    * Get Content.
    *
    * @return varchar
    */
    public function getContent();

   /**
    * Set Content.
    */
    public function setContent($content);

   /**
    * Get Publish Date.
    *
    * @return varchar
    */
    public function getPublishDate();

   /**
    * Set PublishDate.
    */
    public function setPublishDate($publishDate);

   /**
    * Get IsActive.
    *
    * @return varchar
    */
    public function getIsActive();

   /**
    * Set StartingPrice.
    */
    public function setIsActive($isActive);

   /**
    * Get UpdateTime.
    *
    * @return varchar
    */
    public function getUpdateTime();

   /**
    * Set UpdateTime.
    */
    public function setUpdateTime($updateTime);

   /**
    * Get CreatedAt.
    *
    * @return varchar
    */
    public function getCreatedAt();

   /**
    * Set CreatedAt.
    */
    public function setCreatedAt($createdAt);
}
