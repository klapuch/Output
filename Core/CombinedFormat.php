<?php
declare(strict_types = 1);
namespace Klapuch\Output;

/**
 * Multiple combined formats acting like a single one
 */
final class CombinedFormat implements Format {
	private $formats;

	public function __construct(Format ...$formats) {
		$this->formats = $formats;
	}

	public function with($key, $content = null): Format {
		$last = array_pop($this->formats);
		return new self(
			...array_merge($this->formats, [$last->with($key, $content)])
		);
	}

	public function adjusted($key, callable $adjustment): Format {
		return new self(
			...array_map(
				function(Format $format) use ($key, $adjustment): Format {
					return $format->adjusted($key, $adjustment);
				},
				$this->formats
			)
		);
	}

	public function serialization(): string {
		return array_reduce(
			$this->formats,
			function(string $combination, Format $format): string {
				$combination .= $format->serialization();
				return $combination;
			},
			''
		);
	}
}