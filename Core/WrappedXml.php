<?php
declare(strict_types = 1);
namespace Klapuch\Output;

/**
 * Wrapped whole XML into particular tag
 */
final class WrappedXml implements Format {
	private const EMPTY_XML = '';
	private $wrap;
	private $formats;

	public function __construct(string $wrap, Format ...$formats) {
		$this->wrap = $wrap;
		$this->formats = $formats;
	}

	public function with($tag, $content = null): Format {
		return new self(
			$this->wrap,
			...array_map(function(Format $format) use ($tag, $content): Format {
				return $format->with($tag, $content);
			}, $this->formats)
		);
	}

	public function adjusted($tag, callable $adjustment): Format {
		return new self(
			$this->wrap,
			...array_map(
				function(Format $format) use ($tag, $adjustment): Format {
					return $format->adjusted($tag, $adjustment);
				},
				$this->formats
			)
		);
	}

	public function serialization(): string {
		return $this->wrap(
			array_reduce(
				$this->formats,
				function(string $wrapped, Format $format): string {
					$wrapped .= $format->serialization();
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
		preg_match('~^(<\?xml.*\?>)~', $content, $match);
		return sprintf(
			'<%1$s>%2$s</%1$s>',
			$this->wrap,
			str_replace(current($match), '', $content)
		);
	}
}