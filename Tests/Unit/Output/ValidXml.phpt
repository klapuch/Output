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
			))->serialize()
		);
	}

	/**
	 * @throws \InvalidArgumentException XML is not valid
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
		))->serialize();
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
		))->serialize();
		Assert::false(libxml_use_internal_errors(false));
	}
}

(new ValidXml())->run();