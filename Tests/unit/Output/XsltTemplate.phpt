<?php
/**
 * @testCase
 * @phpVersion > 5.6
 */
namespace Klapuch\Unit\Output;

use Klapuch\Output;
use Tester;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';

final class XsltTemplate extends Tester\TestCase {
	public function testOutputOfExistingTemplateWithValidData() {
		$template = Tester\FileMock::create(
			'<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:template match="root">
	<p><xsl:value-of select="."/></p>
</xsl:template>
</xsl:stylesheet>'
		);
		$data = new \SimpleXMLElement('<root>Data</root>');
		Assert::contains(
			'<p>Data</p>',
			(new Output\XsltTemplate($template, $data))->render()
		);
	}
}

(new XsltTemplate())->run();
