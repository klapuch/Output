<?php
declare(strict_types = 1);

namespace Klapuch\Output;

/**
 * Format filled by set
 */
final class FilledFormat implements Format {
	/** @var \Klapuch\Output\Format */
	private $origin;

	/** @var mixed[] */
	private $set;

	public function __construct(Format $origin, array $set) {
		$this->origin = $origin;
		$this->set = $set;
	}

	/**
	 * @param mixed $tag
	 * @param mixed|null $content
	 * @return \Klapuch\Output\Format
	 */
	public function with($tag, $content = null): Format {
		return $this->fill($this->set)->with($tag, $content);
	}

	public function serialization(): string {
		return $this->fill($this->set)->serialization();
	}

	/**
	 * @param mixed $tag
	 * @param callable $adjustment
	 * @return \Klapuch\Output\Format
	 */
	public function adjusted($tag, callable $adjustment): Format {
		return $this->fill($this->set)->adjusted($tag, $adjustment);
	}

	private function fill(array $set): Format {
		return array_reduce(
			array_keys($this->set),
			static function(Format $format, string $name) use ($set): Format {
				return $format->with($name, $set[$name]);
			},
			$this->origin
		);
	}
}
