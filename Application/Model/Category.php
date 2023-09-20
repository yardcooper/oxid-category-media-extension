<?php

namespace Novikovdvpoit\CategoryExtendModule\Application\Model;

/**
 * Category manager extension.
 * Collects category documents and media files and urls
 *
 */
class Category extends Category_parent
{
    /**
     * The list of article media URLs
     *
     * @var string
     */
    protected $_aMediaUrls = null;

    /**
     * Return article media URL
     *
     * @return array
     */
    public function getMediaUrls()
    {
        if ($this->_aMediaUrls === null) {
            $this->_aMediaUrls = oxNew(\OxidEsales\Eshop\Core\Model\ListModel::class);
            $this->_aMediaUrls->init("oxmediaurl");
            $this->_aMediaUrls->getBaseObject()->setLanguage($this->getLanguage());

            $sViewName = getViewName("oxmediaurls", $this->getLanguage());
            $sQ = "select * from {$sViewName} where oxobjectid = :oxobjectid";
            $this->_aMediaUrls->selectString($sQ, [
                ':oxobjectid' => $this->getId()
            ]);
        }

        return $this->_aMediaUrls;
    }
}