<?php
declare(strict_types = 1);
namespace Klapuch\Output;

/**
 * Wrapped whole XML into particular tag
 */
final class WrappedXml implements Format {
	const EMPTY_XML = '';
	private $wrap;
	private $formats;

	public function __construct(string $wrap, Format ...$formats) {
		$this->wrap = $wrap;
		$this->formats = $formats;
	}

	public function with(string $tag, $content = null): Format {
		return new self(
			$this->wrap,
			...array_map(function(Format $format) use($tag, $content): Format {
				return $format->with($tag, $content);
			}, $this->formats)
		);
	}

	public function serialize(): string {
		return $this->wrap(
			array_reduce(
				$this->formats,
				function(string $wrapped, Format $format): string {
					$wrapped .= $format->serialize();
					return $wrapped;
				},
				self::EMPTY_XML
			)
		);
	}

	/**
	 * Wrapped content
	 * @param string $content
	 * @return string
	 */
	private function wrap(string $content): string {
		return sprintf(
			'<%1$s>%2$s</%1$s>',
			$this->wrap,
			$content
		);
	}
}