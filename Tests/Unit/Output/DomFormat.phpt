<?php
/**
 * @testCase
 * @phpVersion > 7.0
 */
namespace Klapuch\Unit\Output;

use Klapuch\Output;
use Tester;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';

final class DomFormat extends Tester\TestCase {
	public function testSerializingDomAsXml() {
		$dom = new \DOMDocument();
		$dom->loadXML('<root><element>ELEMENT</element></root>');
		Assert::same(
			'<?xml version="1.0"?>
<root><element>ELEMENT</element></root>' . "\n",
			(new Output\DomFormat($dom, 'xml'))->serialization()
		);
	}

	public function testSerializingDomAsHtml() {
		$dom = new \DOMDocument();
		$dom->loadXML('<root><element>ELEMENT</element></root>');
		Assert::same(
			'<root><element>ELEMENT</element></root>' . "\n",
			(new Output\DomFormat($dom, 'html'))->serialization()
		);
	}

	/**
	 * @throws \InvalidArgumentException Format "xsd" is not supported
	 */
	public function testThrowingOnSerializationToUnknownFormat() {
		$dom = new \DOMDocument();
		$dom->loadXML('<root><element>ELEMENT</element></root>');
		(new Output\DomFormat($dom, 'xsd'))->serialization();
	}

	public function testCaseInsensitiveFormatMatch() {
		$dom = new \DOMDocument();
		$dom->loadXML('<root><element>ELEMENT</element></root>');
		Assert::noError(function() use($dom) {
			(new Output\DomFormat($dom, 'XmL'))->serialization();
		});
	}

	public function testAppendingToEmptyFormat() {
		$dom = new \DOMDocument();
		Assert::same(
			'<?xml version="1.0"?>
<root>ROOT</root>' . "\n",
			(new Output\DomFormat($dom, 'xml'))
				->with('root', 'ROOT')
				->serialization()
		);
	}

	public function testAppendingAsSibling() {
		$dom = new \DOMDocument();
		$dom->loadXML('<element>ELEMENT</element>');
		Assert::same(
			'<?xml version="1.0"?>
<element>ELEMENT</element>
<root>ROOT</root>' . "\n",
			(new Output\DomFormat($dom, 'xml'))
				->with('root', 'ROOT')
				->serialization()
		);
	}
}

(new DomFormat())->run();