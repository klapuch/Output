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

final class WrappedXml extends Tester\TestCase {
	public function testWrappingEverythingAtOnce() {
		Assert::same(
			'<root><a>A</a><b>B</b></root>',
			(new Output\WrappedXml(
				'root',
				new Output\FakeFormat('<a>A</a>'),
				new Output\FakeFormat('<b>B</b>')
			))->serialization()
		);
	}

	public function testWrappingAppliedOnEveryElement() {
		Assert::same(
			'<root><a>A</a>|x|X|<b>B</b>|x|X|</root>',
			(new Output\WrappedXml(
				'root',
				new Output\FakeFormat('<a>A</a>'),
				new Output\FakeFormat('<b>B</b>')
			))->with('x', 'X')->serialization()
		);
	}

	public function testAdjustingUsingPassedFormats() {
		Assert::same(
			'<root><a>FOO</a><b>bar</b></root>',
			(new Output\WrappedXml(
				'root',
				new Output\Xml(['a' => 'foo']),
				new Output\Xml(['b' => 'bar'])
			))->adjusted('a', 'strtoupper')
			->serialization()
		);
	}

	public function testIgnoringUnknownTag() {
		Assert::same(
			'<root><a>foo</a><b>bar</b></root>',
			(new Output\WrappedXml(
				'root',
				new Output\Xml(['a' => 'foo']),
				new Output\Xml(['b' => 'bar'])
			))->adjusted('xxx', 'strtoupper')
			->serialization()
		);
	}

	public function testWrappingExceptDeclaration() {
		Assert::same(
			'<root><a>A</a><b>B</b></root>',
			(new Output\WrappedXml(
				'root',
				new Output\FakeFormat('<?xml version="1.0" ?><a>A</a>'),
				new Output\FakeFormat('<b>B</b>')
			))->serialization()
		);
	}
}

(new WrappedXml())->run();