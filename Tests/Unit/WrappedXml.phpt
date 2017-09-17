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
			'<root><x><a>FOO</a></x><y><b>bar</b></y></root>',
			(new Output\WrappedXml(
				'root',
				new Output\Xml(['a' => 'foo'], 'x'),
				new Output\Xml(['b' => 'bar'], 'y')
			))->adjusted('a', 'strtoupper')
			->serialization()
		);
	}

	public function testIgnoringUnknownTag() {
		Assert::same(
			'<root><x><a>foo</a></x><y><b>bar</b></y></root>',
			(new Output\WrappedXml(
				'root',
				new Output\Xml(['a' => 'foo'], 'x'),
				new Output\Xml(['b' => 'bar'], 'y')
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