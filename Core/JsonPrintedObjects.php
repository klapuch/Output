<?php
declare(strict_types = 1);

namespace Klapuch\Output;

use Klapuch\Output;

/**
 * Simplified mass printing to JSON
 */
final class JsonPrintedObjects implements Output\Format {
	/** @var object[] */
	private $prints;

	/** @var callable */
	private $response;

	public function __construct(callable $response, object ...$prints) {
		$this->response = $response;
		$this->prints = $prints;
	}

	public function serialization(): string {
		$objects = array_map(function (object $object): string {
			return call_user_func_array($this->response, [$object, new Output\Json()])->serialization();
		}, $this->prints);
		return sprintf('[%s]', implode(',', $objects));
	}

	/**
	 * @param mixed $tag
	 * @param mixed $content
	 */
	public function with($tag, $content = null): Output\Format {
		throw new \Exception('Not implemented');
	}

	/**
	 * @param mixed $tag
	 * @param callable $adjustment
	 */
	public function adjusted($tag, callable $adjustment): Output\Format {
		return new Output\Json(
			array_map(
				static function(Output\Format $format): array {
					return json_decode($format->serialization(), true);
				},
				array_reduce(
					$this->prints,
					function(array $objects, object $object) use ($tag, $adjustment): array {
						$objects[] = call_user_func_array($this->response, [$object, new Output\Json()])->adjusted($tag, $adjustment);
						return $objects;
					},
					[]
				)
			)
		);
	}
}
