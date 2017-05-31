<?php
	namespace DaybreakStudios\Doze;

	use DaybreakStudios\Doze\Errors\AccessDeniedError;
	use DaybreakStudios\Doze\Errors\ApiErrorInterface;
	use DaybreakStudios\Doze\Errors\NotFoundError;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Serializer\SerializerInterface;

	/**
	 * Class Responder
	 *
	 * @package DaybreakStudios\Doze
	 */
	class Responder implements ResponderInterface {
		/**
		 * @var SerializerInterface
		 */
		protected $serializer;

		/**
		 * Responder constructor.
		 *
		 * @param SerializerInterface $serializer
		 */
		public function __construct(SerializerInterface $serializer) {
			$this->serializer = $serializer;
		}

		/**
		 * {@inheritdoc}
		 */
		public function createResponse($format, $data = null, $status = null, array $headers = []) {
			if ($data === null && $status === null)
				$status = Response::HTTP_NO_CONTENT;
			else if ($data !== null)
				$data = $this->serializer->serialize($data, $format);

			return new Response($data, $status ?: Response::HTTP_OK, $headers + [
					'Content-Type' => 'application/' . $format,
				]);
		}

		/**
		 * {@inheritdoc}
		 */
		public function createErrorResponse(ApiErrorInterface $error, $format, $status = null, array $headers = []) {
			if ($status === null)
				$status = $error->getHttpStatus() ?: Response::HTTP_BAD_REQUEST;

			return $this->createResponse($format, [
				'error' => [
					'code' => $error->getCode(),
					'message' => $error->getMessage(),
				],
			], $status, $headers);
		}

		/**
		 * {@inheritdoc}
		 */
		public function createAccessDeniedResponse($format) {
			return $this->createErrorResponse(new AccessDeniedError(), $format);
		}

		/**
		 * {@inheritdoc}
		 */
		public function createNotFoundResponse($format) {
			return $this->createErrorResponse(new NotFoundError(), $format);
		}
	}