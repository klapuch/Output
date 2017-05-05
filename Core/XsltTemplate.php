<?php
declare(strict_types = 1);
namespace Klapuch\Output;

/**
 * XSLT template
 */
final class XsltTemplate implements Template {
	private $template;
	private $stylesheet;

	public function __construct(string $template, Format $stylesheet) {
		$this->template = $template;
		$this->stylesheet = $stylesheet;
	}

	public function render(array $variables = []): string {
		$xsl = new \DOMDocument();
		$xsl->load($this->template);
		$xslt = new \XSLTProcessor();
		$xslt->registerPHPFunctions();
		$xslt->setParameter('', $variables);
		$xslt->importStylesheet($xsl);
		$xml = new \DOMDocument();
		$xml->loadXML($this->stylesheet->serialization());
		return $xslt->transformToXml($xml);
	}
}