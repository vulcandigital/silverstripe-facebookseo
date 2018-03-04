<?php

namespace Vulcan\FacebookSeo\Extensions;

use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\FieldType\DBHTMLText;
use Vulcan\FacebookSeo\Builders\MetaGenerator;

/**
 * Class PageExtension
 * @package Vulcan\FacebookSeo\Extensions
 *
 * @property string FacebookSeoTitle
 * @property string FacebookSeoDescription
 * @property string FacebookSeoType
 * @property \Page  $owner
 *
 * @property int    FacebookSeoImage
 *
 * @method Image FacebookSeoImage()
 */
class PageExtension extends DataExtension
{
    private static $db = [
        'FacebookSeoTitle'       => 'Varchar(255)',
        'FacebookSeoDescription' => 'Text',
        'FacebookSeoType'        => 'Varchar(50)',
    ];

    private static $has_one = [
        'FacebookSeoImage' => Image::class
    ];

    private static $owns = [
        'FacebookSeoImage'
    ];

    private static $cascade_deletes = [
        'FacebookSeoImage'
    ];

    /** @var bool */
    protected $cancelDefaultFbTags = false;

    /**
     * Prevents {@link FacebookMetaTags()} template method from returning anything
     *
     * @param $bool
     */
    public function cancelDefaultFbTags($bool)
    {
        $this->cancelDefaultFbTags = (bool)$bool;
    }

    public function updateCMSFields(FieldList $fields)
    {
        $fields->addFieldsToTab('Root.FacebookSEO', [
            TextField::create('FacebookSeoTitle', 'Title')->setRightTitle('If blank, inherits default page title'),
            TextareaField::create('FacebookSeoDescription', 'Description')->setRightTitle('If blank, inherits meta description if it exists or gets the first 297 characters from content'),
            DropdownField::create('FacebookSeoType', 'Type', MetaGenerator::getValidTypes())->setHasEmptyDefault(true)->setEmptyString('Please select...')->setRightTitle(DBHTMLText::create()->setValue('If unsure, leave blank<br/><br/>View the list definitions <a href="https://developers.facebook.com/docs/reference/opengraph/" target="_blank">here</a>.')),
            UploadField::create('FacebookSeoImage', 'Image')->setRightTitle('Facebook recommends images to be 1200 x 630 pixels')->setFolderName('SEO')
        ]);
    }

    /**
     * Add this method to your global template, you can prevent tags being generated by using {@link cancelDefaultFbTags()}
     * and override MetaTags if you want to customise it differently for a specific {@link DataObject}
     *
     * @return string|null
     */
    public function FacebookMetaTags($force = false)
    {
        if ($this->isCancelDefaultFbTags() && !$force) {
            return null;
        }

        /** @var \Page|static $owner */
        $owner = $this->owner;
        $metaGenerator = MetaGenerator::create();
        $metaGenerator->setTitle($this->getFacebookTitle());
        $metaGenerator->setType('article');
        $metaGenerator->setDescription($this->getFacebookDescription());
        $metaGenerator->setUrl($owner->AbsoluteLink());
        $metaGenerator->setImageUrl($this->getFacebookImage());
        return DBHTMLText::create()->setValue((string)$metaGenerator);
    }

    /**
     * @return bool
     */
    public function isCancelDefaultFbTags()
    {
        return $this->cancelDefaultFbTags;
    }

    /**
     * @return string
     */
    public function getFacebookTitle()
    {
        return $this->getOwner()->FacebookSeoTitle ?: $this->getOwner()->Title;
    }

    /**
     * @return string|null
     */
    public function getFacebookDescription()
    {
        $description = null;

        if (strlen($this->getOwner()->FacebookSeoDescription)) {
            $description = $this->getOwner()->FacebookSeoDescription;
        }

        if (strlen($this->getOwner()->MetaDescription)) {
            $description = $this->getOwner()->MetaDescription;
        }

        if (strlen($this->getOwner()->Content)) {
            $description = strip_tags(ShortcodeParser::get_active()->parse($this->getOwner()->Content));
        }

        return $description;
    }

    /**
     * @return null|string
     */
    public function getFacebookImage()
    {
        if (!$this->getOwner()->FacebookSeoImage()->exists() || !$this->FacebookSeoImage()->isPublished()) {
            return null;
        }

        return $this->getOwner()->FacebookSeoImage()->AbsoluteLink();
    }

    /**
     * @return \Page|static
     */
    public function getOwner()
    {
        /** @var \Page|static $owner */
        $owner = parent::getOwner();

        return $owner;
    }
}
