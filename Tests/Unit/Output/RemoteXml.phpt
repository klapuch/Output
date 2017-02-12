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

	public function testAddingNewEmptyNode() {
		Assert::contains(
			'<outer/>',
			(new Output\RemoteXml(
				Tester\FileMock::create('<root><content>a</content></root>')
			))->with('outer')->serialization()
		);
	}
}

(new RemoteXml())->run();