<?php

namespace Novikovdvpoit\CategoryExtendModule\Application\Controller\Admin;

/**
 * Admin categories manager.
 * There is possibility to change categories description, sorting, range of price,
 * media files, urls, documents and etc.
 * Admin Menu: Manage Products -> Categories -> Main.
 */
class CategoryMain extends CategoryMain_parent
{
    const NEW_CATEGORY_ID = "-1";

    /**
     * Loads article category data,
     * returns the name of the template file.
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        /** @var \OxidEsales\Eshop\Application\Model\Category $oCategory */
        $oCategory = $this->createCategory();
        $categoryId = $this->getEditObjectId();

        $this->_aViewData["edit"] = $oCategory;
        $this->_aViewData["oxid"] = $categoryId;

        // Current category exists
        if (isset($categoryId) && $categoryId != self::NEW_CATEGORY_ID) {
            // Load object based on empty Category entity
            $oCategory->loadInLang($this->_iEditLang, $categoryId);
        }

        // Load media files
        $this->_aViewData['aMediaUrls'] = $oCategory->getMediaUrls();

        return "category_main.tpl";
    }

    /**
     * Saves modified extended category parameters.
     * - documents
     * - media files
     *
     * @return mixed
     */
    public function save()
    {
        parent::save();

        $config = $this->getConfig();
        $file = $config->getUploadedFile("myfile");
        $aMediaFile = $config->getUploadedFile("mediaFile");

        if (is_array($file['name']) && reset($file['name']) || $aMediaFile['name']) {
            $config = $this->getConfig();
            if ($config->isDemoShop()) {
                $oEx = oxNew(\OxidEsales\Eshop\Core\Exception\ExceptionToDisplay::class);
                $oEx->setMessage('ARTICLE_EXTEND_UPLOADISDISABLED');
                \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay($oEx, false);
                return null;
            }
        }

        $soxId = $this->getEditObjectId();

        // Media file saving
        $sMediaUrl = $this->getConfig()->getRequestParameter("mediaUrl");
        $sMediaDesc = $this->getConfig()->getRequestParameter("mediaDesc");

        if (($sMediaUrl && $sMediaUrl != 'http://') || $aMediaFile['name'] || $sMediaDesc) {
            if (!$sMediaDesc) {
                return \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay(
                    'EXCEPTION_NODESCRIPTIONADDED'
                );
            }

            if ((!$sMediaUrl || $sMediaUrl == 'http://') && !$aMediaFile['name']) {
                return \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay('EXCEPTION_NOMEDIAADDED');
            }

            $oMediaUrl = oxNew(\OxidEsales\Eshop\Application\Model\MediaUrl::class);
            $oMediaUrl->setLanguage($this->_iEditLang);
            $oMediaUrl->oxmediaurls__oxisuploaded = new \OxidEsales\Eshop\Core\Field(
                0,
                \OxidEsales\Eshop\Core\Field::T_RAW
            );

            // Handle uploaded file
            if ($aMediaFile['name']) {
                try {
                    $sMediaUrl = \OxidEsales\Eshop\Core\Registry::getUtilsFile()->processFile(
                        'mediaFile',
                        'out/media/'
                    );
                    $oMediaUrl->oxmediaurls__oxisuploaded = new \OxidEsales\Eshop\Core\Field(
                        1,
                        \OxidEsales\Eshop\Core\Field::T_RAW
                    );
                } catch (Exception $e) {
                    return \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay($e->getMessage());
                }
            }

            // Save media url
            $oMediaUrl->oxmediaurls__oxobjectid = new \OxidEsales\Eshop\Core\Field(
                $soxId, \OxidEsales\Eshop\Core\Field::T_RAW
            );
            $oMediaUrl->oxmediaurls__oxurl = new \OxidEsales\Eshop\Core\Field(
                $sMediaUrl,
                \OxidEsales\Eshop\Core\Field::T_RAW
            );
            $oMediaUrl->oxmediaurls__oxdesc = new \OxidEsales\Eshop\Core\Field(
                $sMediaDesc,
                \OxidEsales\Eshop\Core\Field::T_RAW
            );

            $oMediaUrl->save();
        }


        return $this->setEditObjectId($soxId);
    }

    /**
     * Delete media url (with possible linked files)
     */
    public function deletemedia()
    {
        $soxId = $this->getEditObjectId();
        $sMediaId = $this->getConfig()->getRequestParameter("mediaid");
        if ($sMediaId && $soxId) {
            $oMediaUrl = oxNew(\OxidEsales\Eshop\Application\Model\MediaUrl::class);
            $oMediaUrl->load($sMediaId);
            $oMediaUrl->delete();
        }
    }

    /**
     * Updates existing media descriptions
     * @return void
     */
    public function updateMedia()
    {
        $aMediaUrls = $this->getConfig()->getRequestParameter('aMediaUrls');

        if (($aMediaUrls ?? false) && is_array($aMediaUrls)) {
            foreach ($aMediaUrls as $sMediaId => $aMediaParams) {
                $oMedia = oxNew(\OxidEsales\Eshop\Application\Model\MediaUrl::class);
                if ($oMedia->load($sMediaId)) {
                    $oMedia->setLanguage(0);
                    $oMedia->assign($aMediaParams);
                    $oMedia->setLanguage($this->_iEditLang);
                    $oMedia->save();
                }
            }
        }
    }
}
