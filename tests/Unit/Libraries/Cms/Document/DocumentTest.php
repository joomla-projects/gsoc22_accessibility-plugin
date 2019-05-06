<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Document
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Tests\Unit\Libraries\Cms\Html;

use Joomla\CMS\Document\Document;
use Joomla\CMS\Document\FactoryInterface;
use Joomla\CMS\Document\HtmlDocument;
use Joomla\CMS\Document\RawDocument;
use Joomla\CMS\WebAsset\WebAssetManager;
use Joomla\Tests\Unit\UnitTestCase;

/**
 * Test class for Document.
 */
class DocumentTest extends UnitTestCase
{
	/**
	 * Provides constructor data for test methods
	 *
	 * @return  array
	 */
	public function constructData(): array
	{
		return [
			[
				['lineend' => "\12"],
				[
					'lineend' => "\12",
					'charset' => 'utf-8',
					'language' => 'en-gb',
					'direction' => 'ltr',
					'tab' => "\11",
					'link' => '',
					'base' => ''
				]
			],
			[
				['charset' => "euc-jp", 'mediaversion' => '1a2b3c4d'],
				[
					'lineend' => "\12",
					'charset' => 'euc-jp',
					'language' => 'en-gb',
					'direction' => 'ltr',
					'tab' => "\11",
					'link' => '',
					'base' => '',
					'mediaversion' => '1a2b3c4d'
				]
			],
			[
				[
					'language' => "de-de", 'direction' => 'rtl',
					'tab'      => 'Crazy Tab', 'link' => 'http://joomla.org',
					'base'     => 'http://base.joomla.org/dir'
				],
				[
					'lineend' => "\12",
					'charset' => 'utf-8',
					'language' => 'de-de',
					'direction' => 'rtl',
					'tab' => "Crazy Tab",
					'link' => 'http://joomla.org',
					'base' => 'http://base.joomla.org/dir'
				]
			]
		];
	}

	/**
	 * @param   array  $options  Options array to inject
	 * @param   array  $expects  Expected data values
	 *
	 * @dataProvider constructData
	 */
	public function testInjectingOptionsIntoTheObjectConstructor($options, $expects)
	{
		$document = $this->createDocument($options);

		$this->assertAttributeSame($expects['lineend'], '_lineEnd', $document);
		$this->assertAttributeSame($expects['charset'], '_charset', $document);
		$this->assertAttributeSame($expects['language'], 'language', $document);
		$this->assertAttributeSame($expects['direction'], 'direction', $document);
		$this->assertAttributeSame($expects['tab'], '_tab', $document);
		$this->assertAttributeSame($expects['link'], 'link', $document);
		$this->assertAttributeSame($expects['base'], 'base', $document);
	}

	/**
	 * @testdox  Test retrieving an instance of JDocumentHtml
	 */
	public function testRetrievingAnInstanceOfTheHtmlDocument()
	{
		$this->assertInstanceOf(
			HtmlDocument::class,
			Document::getInstance('html',  $this->getDocumentDependencyMocks())
		);
	}

	/**
	 * @testdox  Test retrieving non-existing JDocument type returns a JDocumentRaw instance
	 */
	public function testRetrievingANonExistantTypeFetchesARawDocument()
	{
		$type = 'does-not-exist';

		$document = Document::getInstance($type, $this->getDocumentDependencyMocks());

		$this->assertInstanceOf(RawDocument::class, $document);
		$this->assertAttributeSame($type, '_type', $document);
	}

	/**
	 * @testdox  Test that setType returns an instance of $this
	 */
	public function testEnsureSetTypeReturnsThisObject()
	{
		$document = $this->createDocument();

		$this->assertSame($document, $document->setType('raw'));
	}

	/**
	 * @testdox  Test the default return for getType is null
	 */
	public function testTheDefaultReturnForGetTypeIsNull()
	{
		$this->assertNull($this->createDocument()->getType());
	}

	/**
	 * @testdox  Test that setBuffer returns an instance of $this
	 */
	public function testEnsureSetBufferReturnsThisObject()
	{
		$document = $this->createDocument();

		$this->assertSame($document, $document->setBuffer('My awesome content'));

		// Cleanup
		$document::$_buffer = null;
	}

	/**
	 * @testdox  Test the default return for getBuffer is null
	 */
	public function testTheDefaultReturnForGetBufferIsNull()
	{
		$this->assertNull($this->createDocument()->getBuffer());
	}

	/**
	 * @testdox  Test that setMetadata with the 'generator' param returns an instance of $this
	 */
	public function testEnsureSetMetadataForGeneratorReturnsThisObject()
	{
		$document = $this->createDocument();

		$this->assertSame($document, $document->setMetaData('generator', 'My Custom Generator'));
	}

	/**
	 * @testdox  Test the default return for getMetaData with 'generator' param
	 */
	public function testTheDefaultReturnForGetMetaDataWithGenerator()
	{
		$this->assertSame('Joomla! - Open Source Content Management', $this->createDocument()->getMetaData('generator'));
	}

	/**
	 * @testdox  Test that setMetadata with the 'description' param returns an instance of $this
	 */
	public function testEnsureSetMetadataForDescriptionReturnsThisObject()
	{
		$document = $this->createDocument();

		$this->assertSame($document, $document->setMetaData('description', 'My Description'));
	}

	/**
	 * @testdox  Test the default return for getMetaData with 'description' param
	 */
	public function testTheDefaultReturnForGetMetaDataWithDescription()
	{
		$this->assertEmpty($this->createDocument()->getMetaData('description'));
	}

	/**
	 * @testdox  Test that setMetadata with a custom param returns an instance of $this
	 */
	public function testEnsureSetMetadataForCustomParamsReturnsThisObject()
	{
		$document = $this->createDocument();

		$this->assertSame($document, $document->setMetaData('myMetaTag', 'myMetaContent'));
	}

	/**
	 * @testdox  Test the return for getMetaData with a custom param and HTTP-Equiv flag true with data not set to HTTP-Equiv
	 */
	public function testTheReturnForGetMetaDataWithCustomParamAndHttpEquivTrueAndDataNotSet()
	{
		$document = $this->createDocument();

		$document->setMetaData('myMetaTag', 'myMetaContent');

		$this->assertEmpty($document->getMetaData('myMetaTag', true));
	}

	/**
	 * @testdox  Test the return for getMetaData with a custom param and HTTP-Equiv flag true with data set to HTTP-Equiv
	 */
	public function testTheReturnForGetMetaDataWithCustomParamAndHttpEquivTrueAndDataSet()
	{
		$document = $this->createDocument();

		$document->setMetaData('myMetaTag', 'myMetaContent', true);

		$this->assertSame('myMetaContent', $document->getMetaData('myMetaTag', true));
	}

	/**
	 * @testdox  Test the return for getMetaData with a custom param and HTTP-Equiv flag false with data set to HTTP-Equiv
	 */
	public function testTheReturnForGetMetaDataWithCustomParamAndHttpEquivFalseAndDataNotSet()
	{
		$document = $this->createDocument();

		$document->setMetaData('myMetaTag', 'myMetaContent', true);

		$this->assertEmpty($document->getMetaData('myMetaTag'));
	}

	/**
	 * @testdox  Test the return for getMetaData with a custom param and HTTP-Equiv flag false with data not set to HTTP-Equiv
	 */
	public function testTheReturnForGetMetaDataWithCustomParamAndHttpEquivFalseAndDataSet()
	{
		$document = $this->createDocument();

		$document->setMetaData('myMetaTag', 'myMetaContent');

		$this->assertSame('myMetaContent', $document->getMetaData('myMetaTag'));
	}

	/**
	 * @testdox  Test that addScript returns an instance of $this
	 */
	public function testEnsureAddScriptReturnsThisObject()
	{
		$document = $this->createDocument();

		$this->assertSame($document, $document->addScript('https://www.joomla.org/media/system/js/core.js'));
	}

	/**
	 * @testdox  Test that addScriptDeclaration returns an instance of $this
	 */
	public function testEnsureAddScriptDeclarationReturnsThisObject()
	{
		$document = $this->createDocument();

		$this->assertSame($document, $document->addScriptDeclaration('<script>this.window.close();</script>'));
	}

	/**
	 * @testdox  Test that calling addScriptDeclaration twice returns an instance of $this
	 */
	public function testEnsureTwoAddScriptDeclarationCallsReturnsThisObject()
	{
		$document = $this->createDocument();

		$this->assertSame($document, $document->addScriptDeclaration('<script>this.document.id();</script>'));
		$this->assertSame($document, $document->addScriptDeclaration('<script>this.window.close();</script>'));
	}

	/**
	 * @testdox  Test that addStyleSheet returns an instance of $this
	 */
	public function testEnsureAddStylesheetReturnsThisObject()
	{
		$document = $this->createDocument();

		$this->assertSame($document, $document->addStyleSheet('https://www.joomla.org/media/system/css/system.css'));
	}

	/**
	 * @testdox  Test that addStyleDeclaration returns an instance of $this
	 */
	public function testEnsureAddStyleDeclarationReturnsThisObject()
	{
		$document = $this->createDocument();

		$this->assertSame($document, $document->addStyleDeclaration('<style>div { padding: 0; }</style>'));
	}

	/**
	 * @testdox  Test that calling addStyleDeclaration twice returns an instance of $this
	 */
	public function testEnsureTwoAddStyleDeclarationCallsReturnsThisObject()
	{
		$document = $this->createDocument();

		$this->assertSame($document, $document->addStyleDeclaration('<style>div { padding: 0; }</style>'));
		$this->assertSame($document, $document->addStyleDeclaration('<style>h1 { font-size: 4px; }</style>'));
	}

	/**
	 * @testdox  Test that setCharset returns an instance of $this
	 */
	public function testEnsureSetCharsetReturnsThisObject()
	{
		$document = $this->createDocument();

		$this->assertSame($document, $document->setCharset('utf-8'));
	}

	/**
	 * @testdox  Test the default return for getCharset
	 */
	public function testTheDefaultReturnForGetCharset()
	{
		$this->assertSame('utf-8', $this->createDocument()->getCharset());
	}

	/**
	 * @testdox  Test that setLanguage returns an instance of $this
	 */
	public function testEnsureSetLanguageReturnsThisObject()
	{
		$document = $this->createDocument();

		$this->assertSame($document, $document->setLanguage('de-de'));
	}

	/**
	 * @testdox  Test the default return for getLanguage
	 */
	public function testTheDefaultReturnForGetLanguage()
	{
		$this->assertSame('en-gb', $this->createDocument()->getLanguage());
	}

	/**
	 * @testdox  Test that setDirection returns an instance of $this
	 */
	public function testEnsureSetDirectionReturnsThisObject()
	{
		$document = $this->createDocument();

		$this->assertSame($document, $document->setDirection('rtl'));
	}

	/**
	 * @testdox  Test the default return for getDirection
	 */
	public function testTheDefaultReturnForGetDirection()
	{
		$this->assertSame('ltr', $this->createDocument()->getDirection());
	}

	/**
	 * @testdox  Test that setTitle returns an instance of $this
	 */
	public function testEnsureSetTitleReturnsThisObject()
	{
		$document = $this->createDocument();

		$this->assertSame($document, $document->setTitle('Joomla! Rocks'));
	}

	/**
	 * @testdox  Test the default return for getTitle
	 */
	public function testTheDefaultReturnForGetTitle()
	{
		$this->assertEmpty($this->createDocument()->getTitle());
	}

	/**
	 * @testdox  Test that setBase returns an instance of $this
	 */
	public function testEnsureSetBaseReturnsThisObject()
	{
		$document = $this->createDocument();

		$this->assertSame($document, $document->setBase('https://www.joomla.org'));
	}

	/**
	 * @testdox  Test the default return for getBase
	 */
	public function testTheDefaultReturnForGetBase()
	{
		$this->assertEmpty($this->createDocument()->getBase());
	}

	/**
	 * @testdox  Test that setDescription returns an instance of $this
	 */
	public function testEnsureSetDescriptionReturnsThisObject()
	{
		$document = $this->createDocument();

		$this->assertSame($document, $document->setDescription('Joomla!'));
	}

	/**
	 * @testdox  Test the default return for getDescription
	 */
	public function testTheDefaultReturnForGetDescription()
	{
		$this->assertEmpty($this->createDocument()->getDescription());
	}

	/**
	 * @testdox  Test that setLink returns an instance of $this
	 */
	public function testEnsureSetLinkReturnsThisObject()
	{
		$document = $this->createDocument();

		$this->assertSame($document, $document->setLink('https://www.joomla.org'));
	}

	/**
	 * @testdox  Test the default return for getLink
	 */
	public function testTheDefaultReturnForGetLink()
	{
		$this->assertEmpty($this->createDocument()->getLink());
	}

	/**
	 * @testdox  Test that setGenerator returns an instance of $this
	 */
	public function testEnsureSetGeneratorReturnsThisObject()
	{
		$document = $this->createDocument();

		$this->assertSame($document, $document->setGenerator('Joomla! Content Management System'));
	}

	/**
	 * @testdox  Test the default return for getGenerator
	 */
	public function testTheDefaultReturnForGetGenerator()
	{
		$this->assertSame('Joomla! - Open Source Content Management', $this->createDocument()->getGenerator());
	}

	/**
	 * @testdox  Test that setModifiedDate returns an instance of $this
	 */
	public function testEnsureSetModifiedDateReturnsThisObject()
	{
		$document = $this->createDocument();

		$this->assertSame($document, $document->setModifiedDate('2014-10-17'));
	}

	/**
	 * @testdox  Test the default return for getModifiedDate
	 */
	public function testTheDefaultReturnForGetModifiedDate()
	{
		$this->assertEmpty($this->createDocument()->getModifiedDate());
	}

	/**
	 * @testdox  Test that setMimeEncoding returns an instance of $this
	 */
	public function testEnsureSetMimeEncodingReturnsThisObject()
	{
		$document = $this->createDocument();

		$this->assertSame($document, $document->setMimeEncoding('application/json'));
	}

	/**
	 * @testdox  Test the default return for getMimeEncoding
	 */
	public function testTheDefaultReturnForGetMimeEncoding()
	{
		$this->assertEmpty($this->createDocument()->getMimeEncoding());
	}

	/**
	 * @testdox  Test that setLineEnd with param 'win' returns an instance of $this
	 */
	public function testEnsureSetLineEndWinReturnsThisObject()
	{
		$document = $this->createDocument();

		$this->assertSame($document, $document->setLineEnd('win'));
	}

	/**
	 * @testdox  Test that setLineEnd with param 'unix' returns an instance of $this
	 */
	public function testEnsureSetLineEndUnixReturnsThisObject()
	{
		$document = $this->createDocument();

		$this->assertSame($document, $document->setLineEnd('unix'));
	}

	/**
	 * @testdox  Test that setLineEnd with param 'mac' returns an instance of $this
	 */
	public function testEnsureSetLineEndMacReturnsThisObject()
	{
		$document = $this->createDocument();

		$this->assertSame($document, $document->setLineEnd('mac'));
	}

	/**
	 * @testdox  Test that setLineEnd with a custom param returns an instance of $this
	 */
	public function testEnsureSetLineEndCustomReturnsThisObject()
	{
		$document = $this->createDocument();

		$this->assertSame($document, $document->setLineEnd('special'));
	}

	/**
	 * @testdox  Test the default return for _getLineEnd
	 */
	public function testTheDefaultReturnForGetLineEnd()
	{
		$this->assertSame("\12", $this->createDocument()->_getLineEnd());
	}

	/**
	 * @testdox  Test that setTab with a custom param returns an instance of $this
	 */
	public function testEnsureSetTabReturnsThisObject()
	{
		$document = $this->createDocument();

		$this->assertSame($document, $document->setTab("\t"));
	}

	/**
	 * @testdox  Test the default return for _getTab
	 */
	public function testTheDefaultReturnForGetTab()
	{
		$this->assertSame("\11", $this->createDocument()->_getTab());
	}

	/**
	 * @testdox  Test that loadRenderer returns the intended object
	 *
	 * @covers   JDocument::loadRenderer
	 * @uses     JDocument::setType
	 */
	public function testEnsureLoadRendererReturnsCorrectObjectFromFactory()
	{
		$documentDependencyMocks = $this->getDocumentDependencyMocks();
		$documentDependencyMocks['factory']
			->expects($this->once())
			->method('createRenderer');

		$document = $this->createDocument($documentDependencyMocks);

		$document->loadRenderer('head');
	}

	/**
	 * @testdox  Test that parse returns an instance of $this
	 */
	public function testEnsureParseReturnsThisObject()
	{
		$document = $this->createDocument();

		$this->assertSame($document, $document->parse());
	}

	/**
	 * Helper function to create a document with mocked dependencies
	 *
	 * @param array $options
	 *
	 * @return Document
	 */
	protected function createDocument(array $options = []): Document
	{
		$mergedOptions = array_merge($this->getDocumentDependencyMocks(), $options);

		$object = new Document($mergedOptions);

		return $object;
	}

	/**
	 * Helper function to get mocked constructor dependencies of the document
	 *
	 * @return array
	 */
	protected function getDocumentDependencyMocks(): array
	{
		return [
			'factory' => $this->createMock(FactoryInterface::class),
			'webAssetManager' =>  $this->createMock(WebAssetManager::class),
		];
	}
}
