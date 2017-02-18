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

final class RemoteXml extends Tester\TestCase {
	public function testLoadingFromExistingSource() {
		Assert::same(
			'<root><content>a</content></root>',
			(new Output\RemoteXml(
				Tester\FileMock::create('<root><content>a</content></root>')
			))->serialization()
		);
	}

	public function testCorrectEncoding() {
		Assert::same(
			'<root>Příliš žluťoučký kůň úpěl ďábelské ódy.</root>',
			(new Output\RemoteXml(
				Tester\FileMock::create(
					'<root>Příliš žluťoučký kůň úpěl ďábelské ódy.</root>'
				)
			))->serialization()
		);
	}

	public function testAddingNewSimpleNode() {
		Assert::contains(
			'<outer>OUTER</outer>',
			(new Output\RemoteXml(
				Tester\FileMock::create('<root><content>a</content></root>')
			))->with('outer', 'OUTER')->serialization()
		);
	}

	public function testNotAffectingFileDuringConcatening() {
		$content = '<root><content>a</content></root>';
		$file = Tester\FileMock::create($content);
		Assert::contains(
			'<outer>OUTER</outer>',
			(new Output\RemoteXml($file))
			->with('outer', 'OUTER')
			->serialization()
		);
		Assert::same($content, file_get_contents($file));
	}

	public function testAddingNewEmptyNode() {
		Assert::contains(
			'<outer/>',
			(new Output\RemoteXml(
				Tester\FileMock::create('<root><content>a</content></root>')
			))->with('outer')->serialization()
		);
	}

	public function testNotAffectingFileDuringAdjusting() {
		$content = '<root><content>foo</content></root>';
		$file = Tester\FileMock::create($content);
		Assert::contains(
			'<root><content>FOO</content></root>',
			(new Output\RemoteXml($file))
			->adjusted('content', 'strtoupper')
			->serialization()
		);
		Assert::same($content, file_get_contents($file));
	}

	public function testIgnoringUnknownTagToBeAdjusted() {
		$dom = new \DOMDocument();
		$dom->loadXML('<root></root>');
		Assert::same(
			'<?xml version="1.0"?>
<root><content>foo</content></root>
',
			(new Output\RemoteXml(
				Tester\FileMock::create('<root><content>foo</content></root>')
			))->adjusted('foo', 'strtoupper')
			->serialization()
		);
	}
}

(new RemoteXml())->run();