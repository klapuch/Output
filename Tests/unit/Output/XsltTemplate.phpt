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

final class XsltTemplate extends Tester\TestCase {
	public function testOutputOfExistingTemplateWithValidData() {
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

    public function testOutputOfExistingTemplateWithExtraXMLDeclaration() {
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

    public function testOutputWithVariables() {
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

}

(new XsltTemplate())->run();
