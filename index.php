<?php

require 'vendor/autoload.php';

$schema = [
	'type' => 'object',
	'properties' => [
		'a11y' => [
			'type' => 'boolean',
			'default' => false,
		],
	],
];

$data = [
	'a11y' => true,
	'nope' => 123,
];

$validator = new JsonSchema\Validator;
$validator->validate($data, $schema, JsonSchema\Constraints\Constraint::CHECK_MODE_TYPE_CAST);
var_dump($validator->isValid());
print_r($validator->getErrors());
