<?php
declare(strict_types = 1);

namespace Klapuch\Output;

/**
 * Format moving keys by your choice
 */
final class MovingFormat implements Format {
	/** @var \Klapuch\Output\Format */
	private $origin;

	/** @var mixed[] */
	private $source;

	/** @var mixed[] */
	private $moves;

	public function __construct(Format $origin, array $source, array $moves) {
		$this->origin = $origin;
		$this->source = $source;
		$this->moves = $moves;
	}

	/**
	 * @param mixed $tag
	 * @param mixed|null $content
	 * @return \Klapuch\Output\Format
	 */
	public function with($tag, $content = null): Format {
		return $this->origin->with($tag, $content);
	}

	public function serialization(): string {
		return $this->self()->serialization();
	}

	/**
	 * @param mixed $tag
	 * @param callable $adjustment
	 * @return \Klapuch\Output\Format
	 */
	public function adjusted($tag, callable $adjustment): Format {
		return $this->self()->adjusted($tag, $adjustment);
	}

	private function self(): FilledFormat {
		return new FilledFormat(
			$this->origin,
			$this->copy($this->moves($this->source, $this->moves), $this->source)
		);
	}

	/**
	 * Copy moves to source
	 * @param array $moves
	 * @param array $source
	 * @return array
	 */
	private function copy(array $moves, array $source): array {
		return array_reduce(
			array_keys($moves),
			function(array $copies, $field) use ($source, $moves): array {
				if (is_array($moves[$field]))
					$copies[$field] = $this->copy($moves[$field], $source);
				else $copies[$moves[$field]] = $source[$moves[$field]];
				return $copies;
			},
			[]
		);
	}

	private function moves(array $source, array $moves): array {
		return array_filter(
			array_replace_recursive($source, $moves),
			static function($key) use ($moves): bool {
				return is_int($key) || is_array($moves[$key] ?? null);
			},
			ARRAY_FILTER_USE_KEY
		);
	}
}
