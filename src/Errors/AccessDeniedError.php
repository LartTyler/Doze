<?php
	namespace DaybreakStudios\Doze\Errors;

	use Symfony\Component\HttpFoundation\Response;

	/**
	 * Indicates that some part of an API call resulted in an attempt to access a resource the user is not permitted to.
	 *
	 * Code: access_denied
	 * Status: 403
	 *
	 * @package DaybreakStudios\DozeBundle\Error
	 */
	class AccessDeniedError extends ApiError {
		/**
		 * AccessDeniedError constructor.
		 */
		public function __construct() {
			parent::__construct('access_denied', 'Access Denied', Response::HTTP_FORBIDDEN);
		}
	}