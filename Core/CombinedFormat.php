<?php
declare(strict_types = 1);

namespace Klapuch\Output;

/**
 * Multiple combined formats acting like a single one
 */
final class CombinedFormat implements Format {
	/** @var \Klapuch\Output\Format[] */
	private $formats;

	public function __construct(Format ...$formats) {
		$this->formats = $formats;
	}

	/**
	 * @param mixed $key
	 * @param mixed|null $content
	 * @return \Klapuch\Output\Format
	 */
	public function with($key, $content = null): Format {
		$last = array_pop($this->formats);
		if ($last === null) {
			return $this;
		}
		return new self(...array_merge($this->formats, [$last->with($key, $content)]));
	}

	/**
	 * @param mixed $key
	 * @param callable $adjustment
	 * @return \Klapuch\Output\Format
	 */
	public function adjusted($key, callable $adjustment): Format {
		return new self(
			...array_map(
				static function(Format $format) use ($key, $adjustment): Format {
					return $format->adjusted($key, $adjustment);
				},
				$this->formats
			)
		);
	}

	public function serialization(): string {
		return array_reduce(
			$this->formats,
			static function(string $combination, Format $format): string {
				return $combination . $format->serialization();
			},
			''
		);
	}
}
