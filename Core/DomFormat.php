<?php
declare(strict_types = 1);
namespace Klapuch\Output;

/**
 * Format created from DOM
 */
final class DomFormat implements Format {
	const OUTPUTS = [
		'xml' => 'saveXml',
		'html' => 'saveHtml',
	];
	private $dom;
	private $output;

	public function __construct(\DOMDocument $dom, string $output) {
		$this->dom = $dom;
		$this->output = $output;
	}

	public function with(string $tag, $content = null): Format {
		$this->dom->appendChild($this->dom->createElement($tag, $content));
		return new self($this->dom, $this->output);
	}

	public function serialization(): string {
		if($this->supported($this->output))
			return $this->dom->{self::OUTPUTS[strtolower($this->output)]}();
		throw new \InvalidArgumentException(
			sprintf('Format "%s" is not supported', $this->output)
		);
	}

	/**
	 * Is the given output supported?
	 * @param string $output
	 * @return bool
	 */
	private function supported(string $output): bool {
		return (bool)array_uintersect(
			[$this->output],
			array_keys(self::OUTPUTS),
			'strcasecmp'
		);
	}
}