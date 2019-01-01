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

final class XsltTemplate extends Tester\TestCase {
	public function testRenderingWithCorrectEncoding(): void {
		$template = Tester\FileMock::create(
			'<?xml version="1.0" encoding="UTF-8"?>
			<xsl:stylesheet version="1.0" 
			xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
			<xsl:output method="html" encoding="utf-8"/>
			<xsl:template match="root">
			<p><xsl:value-of select="."/></p>
			</xsl:template>
			</xsl:stylesheet>'
		);
		Assert::same(
			'<p>Příliš žluťoučký kuň</p>',
			trim((new Output\XsltTemplate(
				$template,
				new Output\FakeFormat('<root>Příliš žluťoučký kuň</root>')
			))->render())
		);
	}

	public function testRenderingWitouhtExtraXmlDeclaration(): void {
		$template = Tester\FileMock::create(
			'<?xml version="1.0" encoding="UTF-8"?>
			<xsl:stylesheet version="1.0" 
				 xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
				<xsl:output method="html" encoding="utf-8"/>
				<xsl:template match="root">
					<p><xsl:value-of select="."/></p>
				</xsl:template>
			</xsl:stylesheet>'
		);
		Assert::same(
			'<p>Příliš žluťoučký kuň</p>',
			trim((new Output\XsltTemplate(
				$template,
				new Output\FakeFormat(
					'<?xml version="1.0"?><root>Příliš žluťoučký kuň</root>'
				)
			))->render())
		);
	}

	public function testRenderingWithVariables(): void {
		$template = Tester\FileMock::create(
			'<?xml version="1.0" encoding="UTF-8"?>
			<xsl:stylesheet version="1.0" 
				 xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
				<xsl:output method="html" encoding="utf-8"/>
				<xsl:template match="/">
					<p><xsl:value-of select="$first"/></p>
					<p><xsl:value-of select="$second"/></p>
				</xsl:template>
			</xsl:stylesheet>'
		);
		Assert::contains(
			'<p>První</p><p>Druhý</p>',
			trim((new Output\XsltTemplate(
				$template,
				new Output\FakeFormat(
					'<?xml version="1.0"?><root/>'
				)
			))->render(['first' => 'První', 'second' => 'Druhý']))
		);
	}

	public function testRegisteredFunctions(): void {
		$template = Tester\FileMock::create(
			'<?xml version="1.0" encoding="UTF-8"?>
			<xsl:stylesheet version="1.0" 
				 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
				 xmlns:php="http://php.net/xsl">
				<xsl:output method="html" encoding="utf-8"/>
				<xsl:template match="/">
					<p><xsl:value-of select="php:function(\'strtoupper\', $first)"/></p>
				</xsl:template>
			</xsl:stylesheet>'
		);
		Assert::contains(
			'<p xmlns:php="http://php.net/xsl">FIRST</p>',
			trim((new Output\XsltTemplate(
				$template,
				new Output\FakeFormat(
					'<?xml version="1.0"?><root/>'
				)
			))->render(['first' => 'first']))
		);
	}
}

(new XsltTemplate())->run();
