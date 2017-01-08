<?php
declare(strict_types = 1);
namespace Klapuch\Output;

/**
 * XML available as remote source - file, http, ...
 */
final class RemoteXml implements Format {
	private $source;

	public function __construct(string $source) {
		$this->source = $source;
	}

	public function with(string $tag, $content = null): Format {
		return new MergedXml(
			$this->toDOM($this->source),
			new \SimpleXMLElement(
				sprintf('<%1$s>%2$s</%1$s>', $tag, $content)
			) 
		);
	}

	public function serialize(): string {
		$dom = $this->toDOM($this->source);
		return $dom->saveXML($dom->documentElement);
	}

	/**
	 * Source put to the DOM
	 * @param string $source
	 * @return \DOMDocument
	 */
	private function toDOM(string $source): \DOMDocument {
		$xml = new \DOMDocument('1.0', 'utf-8');
		$xml->load($this->source);
		return $xml;
	}
}