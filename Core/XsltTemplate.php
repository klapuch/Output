<?php
declare(strict_types = 1);
namespace Klapuch\Output;

final class XsltTemplate implements Template {
	private $template;
	private $data;

	public function __construct(string $template, \SimpleXMLElement $data) {
	    $this->template = $template;
		$this->data = $data;
	}

	public function render(): string {
		$xsl = new \DOMDocument();
		$xsl->load($this->template);
		$xslt = new \XSLTProcessor();
		$xslt->importStylesheet($xsl);
		return $xslt->transformToXml($this->data);
	}
}