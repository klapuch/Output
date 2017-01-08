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

final class MergedXml extends Tester\TestCase {
	public function testMergingWithoutAddedXmlDeclaration() {
		$root = new \DOMDocument();
		$root->loadXML('<root><element>ELEMENT</element></root>');
		Assert::same(
			'<?xml version="1.0"?>
<root><element>ELEMENT</element>
<merged>MERGED</merged>
<another>ANOTHER</another></root>',
			trim(
				(new Output\MergedXml(
					$root,
					new \SimpleXMLElement('<merged>MERGED</merged>'),
					new \SimpleXMLElement('<another>ANOTHER</another>')
				))->serialize()
			)
		);
	}

	public function testMergingWithoutMalformingTags() {
		$root = new \DOMDocument();
		$root->loadXML('<root><element>ELEMENT</element></root>');
		Assert::same(
			'<?xml version="1.0"?>
<root><element>ELEMENT</element>
<merged><inner>INNER</inner></merged>
<another>ANOTHER</another></root>',
			trim(
				(new Output\MergedXml(
					$root,
					new \SimpleXMLElement('<merged><inner>INNER</inner></merged>'),
					new \SimpleXMLElement('<another>ANOTHER</another>')
				))->serialize()
			)
		);
	}

	public function testRemovingWhiteSpaces() {
		$root = new \DOMDocument();
		$root->loadXML(pack('H*','EFBBBF') . '<root></root>');
		Assert::same(
			'<?xml version="1.0"?>
<root>
<merged>MERGED</merged></root>',
			(new Output\MergedXml(
				$root,
				new \SimpleXMLElement('<merged>MERGED</merged>     ')
			))->serialize()
		);
	}

	public function testAddingNewSimpleNode() {
		$root = new \DOMDocument();
		$root->loadXML('<root></root>');
		Assert::same(
			'<?xml version="1.0"?>
<root>
<merged>MERGED</merged>
<another>ANOTHER</another></root>',
			(new Output\MergedXml(
				$root,
				new \SimpleXMLElement('<merged>MERGED</merged>')
			))->with('another', 'ANOTHER')->serialize()
		);
	}

	public function testAddingNewXmlNode() {
		$root = new \DOMDocument();
		$root->loadXML('<root></root>');
		Assert::same(
			'<?xml version="1.0"?>
<root>
<merged>MERGED</merged>
<another><inner>ANOTHER</inner></another></root>',
			(new Output\MergedXml(
				$root,
				new \SimpleXMLElement('<merged>MERGED</merged>')
			))->with('another', '<inner>ANOTHER</inner>')->serialize()
		);
	}

	public function testAddingNewEmptyNode() {
		$root = new \DOMDocument();
		$root->loadXML('<root></root>');
		Assert::same(
			'<?xml version="1.0"?>
<root>
<merged>MERGED</merged>
<another/></root>',
			(new Output\MergedXml(
				$root,
				new \SimpleXMLElement('<merged>MERGED</merged>')
			))->with('another')->serialize()
		);
	}
}

(new MergedXml())->run();