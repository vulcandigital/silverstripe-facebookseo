<?php

namespace Vulcan\FacebookSeo\Tests;

use SilverStripe\Dev\FunctionalTest;
use Vulcan\FacebookSeo\Builders\MetaGenerator;

class MetaGeneratorTest extends FunctionalTest
{
    /** @var MetaGenerator */
    protected $generator;

    public function setUp()
    {
        parent::setUp();

        $this->generator = MetaGenerator::create();
    }

    public function testEmptyToString()
    {
        $this->assertEquals('', (string)$this->generator);
    }

    public function testTitle()
    {
        $this->generator->setTitle('Hello World!');
        $this->assertEquals('Hello World!', $this->generator->getTitle());
    }

    public function testDescription()
    {
        $this->generator->setDescription('Hello World!');
        $this->assertEquals('Hello World!', $this->generator->getDescription());
    }

    public function testType()
    {
        $this->generator->setType('article');
        $this->assertEquals('article', $this->generator->getType());

        $this->expectException(\Exception::class);
        $this->generator->setType('not.a.real.type');
    }

    public function testUrl()
    {
        $this->generator->setUrl('https://example.com');
        $this->assertEquals('https://example.com', $this->generator->getUrl());

        $this->expectException(\InvalidArgumentException::class);
        $this->generator->setUrl('/i/should/not/accept/relative/links/');
    }

    public function testImageUrl()
    {
        $this->generator->setImageUrl('https://example.com/image.jpg');
        $this->assertEquals('https://example.com/image.jpg', $this->generator->getImageUrl());

        $this->expectException(\InvalidArgumentException::class);
        $this->generator->setImageUrl('/image.jpg');
    }
}