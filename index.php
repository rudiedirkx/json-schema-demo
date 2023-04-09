<?php

use Nette\Schema\Expect;

require 'vendor/autoload.php';

header('Content-type: text/plain; charset=utf-8');

$data = [
	'a11y' => true,
	// 'nope' => 123,
	'start' => '2023-01-01',
];



$schema = [
	'type' => 'object',
	'properties' => [
		'a11y' => [
			'type' => 'boolean',
			'default' => false,
		],
		'start' => [
			'type' => 'string',
			'format' => 'date',
			// 'default' => false,
		],
	],
];

$validator = new JsonSchema\Validator;
$validator->validate($data, $schema, JsonSchema\Constraints\Constraint::CHECK_MODE_TYPE_CAST);
var_dump($validator->isValid());
print_r($validator->getErrors());



echo "\n\n\n";



class CustomValidationError extends Exception {
	public function __construct(
		string $message,
		protected array $params = [],
		protected ?string $messageType = null,
	) {
		parent::__construct($message);
	}

	public function getParams() : array {
		return $this->params;
	}

	public function getMessageType() : string {
		return $this->messageType ?? Nette\Schema\Message::TYPE_MISMATCH;
	}
}

abstract class CustomType implements Nette\Schema\Schema {
	use Nette\Schema\Elements\Base;

	function normalize($value, Nette\Schema\Context $context) {
// var_dump(__LINE__);
		try {
			$this->validateValue($value);
		}
		catch (CustomValidationError $ex) {
			$context->addError($ex->getMessage(), $ex->getMessageType(), $ex->getParams());
		}

		return $this->doNormalize($value, $context);
	}

	function merge($value, $base) {}

	function complete($value, Nette\Schema\Context $context) {
		return $this->doFinalize($value, $context);
	}
}

class CustomDate extends CustomType {
	protected function validateValue(mixed &$value) : void {
		if (!is_string($value) || !preg_match('#^\d\d\d\d-\d\d-\d\d$#', $value)) {
			throw new CustomValidationError(
				'The %label% %path% must be a date string, %value% given.',
				['value' => is_scalar($value) ? $value : gettype($value)],
			);
		}
	}
}

$schema = Expect::structure([
	'a11y' => Expect::bool(),
	'start' => new CustomDate(),
]);

try {
	$processor = new Nette\Schema\Processor;
	$normalized = $processor->process($schema->castTo('array'), $data);
	var_dump(true);
	// print_r($normalized);
}
catch (Nette\Schema\ValidationException $ex) {
	echo 'Data is invalid: ' . $ex->getMessage() . "\n";
}
