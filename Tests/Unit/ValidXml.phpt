<?php
declare(strict_types = 1);
/**
 * @testCase
 * @phpVersion > 7.1
 */
namespace Klapuch\Output\Unit;

use Klapuch\Output;
use Tester;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

final class ValidXml extends Tester\TestCase {
	public function testValidXmlAgainstSchema() {
		$xml = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
		$xml .= '<root><name>Dominik Klapuch</name></root>' . "\n";
		$xsd = '<xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema">
		  <xs:element name="root">
			<xs:complexType>
			  <xs:sequence>
				<xs:element name="name" type="xs:string"/>
			  </xs:sequence>
			</xs:complexType>
		  </xs:element>
		</xs:schema>';
		Assert::same(
			$xml,
			(new Output\ValidXml(
				new Output\FakeFormat($xml),
				Tester\FileMock::create($xsd)
			))->serialization()
		);
	}

	/**
	 * @throws \UnexpectedValueException XML is not valid: "Element 'name': Element content is not allowed, because the type definition is simple."
	 */
	public function testInvalidXmlAgainstSchema() {
		$xml = '<?xml version="1.0" encoding="utf-8"?>' . PHP_EOL;
		$xml .= '<root><name><first>Dominik</first></name></root>' . PHP_EOL;
		$xsd = '<xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema">
		  <xs:element name="root">
			<xs:complexType>
			  <xs:sequence>
				<xs:element name="name" type="xs:string"/>
			  </xs:sequence>
			</xs:complexType>
		  </xs:element>
		</xs:schema>';
		(new Output\ValidXml(
			new Output\FakeFormat($xml),
			Tester\FileMock::create($xsd)
		))->serialization();
	}

	/**
	 * @throws \UnexpectedValueException XML is not valid: "Element 'name': 'xxx' is not a valid value of the atomic type 'xs:int'. | Element 'second': This element is not expected. Expected is ( title )."
	 */
	public function testMultipleInvalidParts() {
		$xml = '<?xml version="1.0" encoding="utf-8"?>' . PHP_EOL;
		$xml .= '<root><name>xxx</name><second>Klapuch</second></root>' . PHP_EOL;
		$xsd = '<xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema">
		  <xs:element name="root">
			<xs:complexType>
			  <xs:sequence>
				<xs:element name="name" type="xs:int"/>
				<xs:element name="title" type="xs:string"/>
			  </xs:sequence>
			</xs:complexType>
		  </xs:element>
		</xs:schema>';
		(new Output\ValidXml(
			new Output\FakeFormat($xml),
			Tester\FileMock::create($xsd)
		))->serialization();
	}

	public function testLibxmlWithoutChange() {
		$xml = '<?xml version="1.0" encoding="utf-8"?>' . PHP_EOL;
		$xml .= '<root><name>Dominik Klapuch</name></root>' . PHP_EOL;
		$xsd = '<xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema">
		  <xs:element name="root">
			<xs:complexType>
			  <xs:sequence>
				<xs:element name="name" type="xs:string"/>
			  </xs:sequence>
			</xs:complexType>
		  </xs:element>
		</xs:schema>';
		libxml_use_internal_errors(false);
		(new Output\ValidXml(
			new Output\FakeFormat($xml),
			Tester\FileMock::create($xsd)
		))->serialization();
		Assert::false(libxml_use_internal_errors(false));
	}

	/**
	 * @throws \UnexpectedValueException XML is not valid: "Start tag expected, '<' not found | The document has no document element."
	 */
	public function testInvalidXmlAsInput() {
		$xml = 'Dominik Klapuch';
		$xsd = '<xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema">
		  <xs:element name="root">
			<xs:complexType>
			  <xs:sequence>
				<xs:element name="name" type="xs:string"/>
			  </xs:sequence>
			</xs:complexType>
		  </xs:element>
		</xs:schema>';
		Assert::same(
			$xml,
			(new Output\ValidXml(
				new Output\FakeFormat($xml),
				Tester\FileMock::create($xsd)
			))->serialization()
		);
	}
}

(new ValidXml())->run();