<?php
declare(strict_types = 1);
namespace Klapuch\Output;

/**
 * Valid XML determined by XML Schema
 */
final class ValidXml implements Format {
	private $origin;
	private $schema;

	public function __construct(Format $origin, string $schema) {
		$this->origin = $origin;
		$this->schema = $schema;
	}

	public function with($tag, $content = null): Format {
		return $this->origin->with($tag, $content);
	}

	public function adjusted($tag, callable $adjustment): Format {
		return $this->origin->adjusted($tag, $adjustment);
	}

	public function serialization(): string {
		$previous = libxml_use_internal_errors(true);
		try {
			$xml = new \DOMDocument();
			$xml->loadXML($this->origin->serialization());
			if ($xml->schemaValidate($this->schema))
				return $xml->saveXML();
			throw new \UnexpectedValueException(
				sprintf(
					'XML is not valid: "%s"',
					$this->errors(...libxml_get_errors())
				)
			);
		} finally {
			libxml_use_internal_errors($previous);
		}
	}

	private function errors(\LibXMLError ...$errors): string {
		return implode(
			' | ',
			array_map(
				function(\LibXMLError $error): string {
					return trim($error->message);
				},
				$errors
			)
		);
	}
}