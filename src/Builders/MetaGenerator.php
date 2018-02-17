<?php

namespace Vulcan\FacebookSeo\Builders;

use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Injector\Injectable;
use SilverStripe\ORM\FieldType\DBHTMLText;

/**
 * Class MetaGenerator
 * @package Vulcan\FacebookSeo\Builders
 */
class MetaGenerator
{
    use Injectable, Configurable;

    protected $title;

    protected $description;

    protected $type;

    protected $url;

    protected $imageUrl;

    public function forTemplate()
    {
        if ($this->getTitle()) {
            $tags[] = sprintf('<meta property="og:title" content="%s"/>', $this->getTitle());
        }

        if ($this->getDescription()) {
            $tags[] = sprintf('<meta property="og:description" content="%s"/>', $this->getDescription());
        }

        if ($this->getType()) {
            $tags[] = sprintf('<meta property="og:type" content="%s"/>', $this->getType());
        }

        if ($this->getUrl()) {
            $tags[] = sprintf('<meta property="og:url" content="%s"/>', $this->getUrl());
        }

        if ($this->getImageUrl()) {
            $tags[] = sprintf('<meta property="og:image" content="%s"/>', $this->getImageUrl());
        }

        return implode(PHP_EOL, $tags);
    }

    /**
     * @param mixed $title
     *
     * @return MetaGenerator
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @param mixed $description
     *
     * @return MetaGenerator
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @param mixed $type
     *
     * @return MetaGenerator
     * @throws \Exception
     */
    public function setType($type)
    {
        if (!in_array($type, array_keys(static::getValidTypes()))) {
            throw new \Exception("That type [$type] is not a valid type, please see: https://developers.facebook.com/docs/reference/opengraph/");
        }

        $this->type = $type;
        return $this;
    }

    /**
     * @param mixed $url
     *
     * @return MetaGenerator
     */
    public function setUrl($url = null)
    {
        if ($url && (substr($url, 0, 1) === '/' || substr($url, 0, 4) !== 'http')) {
            throw new \InvalidArgumentException('A relative URL was detected, your must provide the full absolute URL instead');
        }

        $this->url = $url;
        return $this;
    }

    /**
     * @param mixed $imageUrl
     *
     * @return MetaGenerator
     */
    public function setImageUrl($imageUrl = null)
    {
        if ($imageUrl && (substr($imageUrl, 0, 1) === '/' || substr($imageUrl, 0, 4) !== 'http')) {
            throw new \InvalidArgumentException('A relative or invalid URL was detected, your must provide the full absolute URL');
        }

        $this->imageUrl = $imageUrl;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        $obj = DBHTMLText::create();

        if (!$this->description) {
            return null;
        }

        return $obj->setValue($this->description)->LimitCharacters(297);
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return mixed
     */
    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    public function __toString()
    {
        return $this->forTemplate();
    }

    public static function getValidTypes()
    {
        return [
            'apps.saves'                => 'apps.saves',
            'books.quotes'              => 'books.quotes',
            'books.rates'               => 'books.rates',
            'books.reads'               => 'books.reads',
            'books.wants_to_read'       => 'books.wants_to_read',
            'fitness.bikes'             => 'fitness.bikes',
            'fitness.runs'              => 'fitness.runs',
            'fitness.walks'             => 'fitness.walks',
            'games.achieves'            => 'games.achieves',
            'games.celebrate'           => 'games.celebrate',
            'games.plays'               => 'games.plays',
            'games.saves'               => 'games.saves',
            'music.listens'             => 'music.listens',
            'music.playlists'           => 'music.playlists',
            'news.publishes'            => 'news.publishes',
            'news.reads'                => 'news.reads',
            'og.follows'                => 'og.follows',
            'og.likes'                  => 'og.likes',
            'pages.saves'               => 'pages.saves',
            'restaurant.visited'        => 'restaurant.visited',
            'restaurant.wants_to_visit' => 'restaurant.wants_to_visit',
            'sellers.rates'             => 'sellers.rates',
            'video.rates'               => 'video.rates',
            'video.wants_to_watch'      => 'video.wants_to_watch',
            'video.watches'             => 'video.watches',
            'article'                   => 'article',
            'book'                      => 'book',
            'books.author'              => 'books.author',
            'books.book'                => 'books.book',
            'books.genre'               => 'books.genre',
            'business.business'         => 'business.business',
            'fitness.course'            => 'fitness.course',
            'game.achievement'          => 'game.achievement',
            'music.album'               => 'music.album',
            'music.playlist'            => 'music.playlist',
            'music.radio_station'       => 'music.radio_station',
            'music.song'                => 'music.song',
            'place'                     => 'place',
            'product'                   => 'product',
            'product.group'             => 'product.group',
            'product.item'              => 'product.item',
            'profile'                   => 'profile',
            'restaurant.menu'           => 'restaurant.menu',
            'restaurant.menu_item'      => 'restaurant.menu_item',
            'restaurant.menu_section'   => 'restaurant.menu_section',
            'restaurant.restaurant'     => 'restaurant.restaurant',
            'video.episode'             => 'video.episode',
            'video.movie'               => 'video.movie',
            'video.other'               => 'video.other',
            'video.tv_show'             => 'video.tv_show',
        ];
    }
}
