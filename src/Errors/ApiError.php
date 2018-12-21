<?php
	namespace DaybreakStudios\Doze\Errors;

	class ApiError implements ApiErrorInterface, ContextAwareInterface {
		/**
		 * @var string
		 */
		protected $code;

		/**
		 * @var string
		 */
		protected $message;

		/**
		 * @var int|null
		 */
		protected $httpStatus;

		/**
		 * @var array
		 */
		protected $context;

		/**
		 * ApiError constructor.
		 *
		 * @param string   $code
		 * @param string   $message
		 * @param int|null $httpStatus
		 * @param array    $context
		 */
		public function __construct($code, $message, $httpStatus = null, array $context = []) {
			$this->code = $code;
			$this->message = $message;
			$this->httpStatus = $httpStatus;
			$this->context = $context;
		}

		/**
		 * @return string
		 */
		public function getCode() {
			return $this->code;
		}

		/**
		 * @return int|null
		 */
		public function getHttpStatus() {
			return $this->httpStatus;
		}

		/**
		 * @return string
		 */
		public function getMessage() {
			return $this->message;
		}

		/**
		 * @return array
		 */
		public function getContext() {
			return $this->context;
		}
	}