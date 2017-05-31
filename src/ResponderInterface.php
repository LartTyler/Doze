<?php
	namespace DaybreakStudios\Doze;

	use DaybreakStudios\Doze\Errors\AccessDeniedError;
	use DaybreakStudios\Doze\Errors\ApiErrorInterface;
	use DaybreakStudios\Doze\Errors\NotFoundError;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;

	/**
	 * Interface ResponderInterface
	 *
	 * @package DaybreakStudios\Doze
	 */
	interface ResponderInterface {
		/**
		 * Creates a response.
		 *
		 * If no value is provided for $data, no body content should be be sent, and the HTTP NO CONTENT
		 * status code should be returned. If any value aside from `null` is used, it should be serialized prior to
		 * being set as the response body.
		 *
		 * @param string     $format  the response format (such as "json" or "xml")
		 * @param mixed|null $data    the response data; if null, no response body should be set, and HTTP_NO_CONTENT
		 *                            should be used as the response status (unless $status is explicitly set)
		 * @param int        $status  the HTTP status; if `null` is provided, the status should be inferred to be 200 OK
		 *                            if $data is not null, or 204 NO CONTENT if it is
		 * @param array      $headers an array of headers to send; this array should take precedence over any default
		 *                            headers (such as Content-Type)
		 * @param array      $context an array containing context options for serialization, in the format
		 *                            "context-key" => "value"
		 *
		 * @return Response
		 */
		public function createResponse($format, $data = null, $status = null, array $headers = [], array $context = []);

		/**
		 * Creates an error response.
		 *
		 * @param ApiErrorInterface $error   an error objecting describing the error that occurred
		 * @param string            $format  the response format (such as "json" or "xml")
		 * @param int|null          $status  the HTTP status; if no status is provided, it should default to
		 *                                   400 BAD REQUEST
		 * @param array             $headers an array of headers to send; this array should take precedence over any
		 *                                   default headers (such as Content-Type)
		 * @param array             $context an array containing context options for serialization, in the format
		 *                                   "context-key" => "value"
		 *
		 * @return Response
		 */
		public function createErrorResponse(ApiErrorInterface $error, $format, $status = null, array $headers = [], array $context = []);

		/**
		 * Creates an error response using AccessDeniedError.
		 *
		 * @param $format string the response format (such as "json" or "xml")
		 *
		 * @return Response
		 * @see AccessDeniedError
		 */
		public function createAccessDeniedResponse($format);

		/**
		 * Creates an error response using NotFoundError.
		 *
		 * @param $format string the response format (such as "json" or "xml")
		 *
		 * @return Response
		 * @see NotFoundError
		 */
		public function createNotFoundResponse($format);
	}