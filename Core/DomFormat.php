<?php
declare(strict_types = 1);
namespace Klapuch\Output;

/**
 * Format created from DOM
 */
final class DomFormat implements Format {
	private const OUTPUTS = [
		'xml' => 'saveXml',
		'html' => 'saveHtml',
	];
	private $dom;
	private $output;

	public function __construct(\DOMDocument $dom, string $output) {
		$this->dom = $dom;
		$this->output = $output;
	}

	public function with($tag, $content = null): Format {
		$this->dom->appendChild($this->dom->createElement($tag, $content));
		return new self($this->dom, $this->output);
	}

	public function adjusted($tag, callable $adjustment): Format {
		foreach ($this->dom->getElementsByTagName($tag) as $element)
			$element->nodeValue = call_user_func($adjustment, $element->nodeValue);
		return $this;
	}

	public function serialization(): string {
		if ($this->supported($this->output))
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
		return (bool) array_uintersect(
			[$output],
			array_keys(self::OUTPUTS),
			'strcasecmp'
		);
	}
}