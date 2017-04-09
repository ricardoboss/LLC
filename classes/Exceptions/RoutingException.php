<?php

namespace Exceptions;

class RoutingException extends LeveledException {
	const UNKNOWN = ['code' => 0, 'level' => LeveledException::LEVEL_INFO];
	const ALREADY_SPECIFIED = ['code' => 1, 'level' => LeveledException::LEVEL_ERROR];
	const INVALID_TYPE = ['code' => 2, 'level' => LeveledException::LEVEL_WARNING];
	const INVALID_CONTROLLER = ['code' => 3, 'level' => LeveledException::LEVEL_ERROR];

	public function __construct(string $message = "", array $type = RoutingException::UNKNOWN, \Throwable $previous =
	null) {
		parent::__construct($message, $type['level'], $type['code'], $previous);
	}
}