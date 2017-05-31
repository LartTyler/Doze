<?php
	namespace DaybreakStudios\Doze\Errors;

	use Symfony\Component\HttpFoundation\Response;

	/**
	 * Indicates a resource was not found.
	 *
	 * Code: not_found
	 * Status: 404
	 *
	 * @package DaybreakStudios\Doze\Errors
	 */
	class NotFoundError extends ApiError {
		/**
		 * NotFoundError constructor.
		 */
		public function __construct() {
			parent::__construct('not_found', 'Not Found', Response::HTTP_NOT_FOUND);
		}
	}