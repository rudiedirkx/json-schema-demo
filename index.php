<?php

use Nette\Schema\Expect;

require 'vendor/autoload.php';

header('Content-type: text/plain; charset=utf-8');

$data = [
	'a11y' => true,
	'nope' => 123,
];



$schema = [
	'type' => 'object',
	'properties' => [
		'a11y' => [
			'type' => 'boolean',
			'default' => false,
		],
	],
];

$validator = new JsonSchema\Validator;
$validator->validate($data, $schema, JsonSchema\Constraints\Constraint::CHECK_MODE_TYPE_CAST);
var_dump($validator->isValid());
print_r($validator->getErrors());


echo "\n\n";


$schema = Expect::structure([
	'a11y' => Expect::bool(),
]);

try {
	$processor = new Nette\Schema\Processor;
	$normalized = $processor->process($schema, $data);
	print_r($normalized);
}
catch (Nette\Schema\ValidationException $ex) {
	echo 'Data is invalid: ' . $ex->getMessage() . "\n";
}
