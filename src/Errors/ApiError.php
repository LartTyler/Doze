<?php
	namespace DaybreakStudios\Doze\Errors;

	class ApiError implements ApiErrorInterface {
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
		 * ApiError constructor.
		 *
		 * @param string   $code
		 * @param string   $message
		 * @param int|null $httpStatus
		 */
		public function __construct($code, $message, $httpStatus = null) {
			$this->code = $code;
			$this->message = $message;
			$this->httpStatus = $httpStatus;
		}

		/**
		 * @return string
		 */
		public function getCode() {
			return $this->code;
		}

		/**
		 * @return string
		 */
		public function getMessage() {
			return $this->message;
		}

		/**
		 * @return int|null
		 */
		public function getHttpStatus() {
			return $this->httpStatus;
		}
	}