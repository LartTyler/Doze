<?php
	namespace DaybreakStudios\Doze\Serializer;

	class FieldSelectorParser {
		const STATE_DATA = 0;
		const STATE_SUBFIELD = 1;

		const TOKEN_FIELD_SEPARATOR = ',';
		const TOKEN_SUBFIELD_OPEN = '{';
		const TOKEN_SUBFIELD_CLOSE = '}';

		/**
		 * @var string
		 */
		protected $selector;

		/**
		 * @var int
		 */
		protected $length;

		/**
		 * @var int
		 */
		protected $pos = 0;

		/**
		 * FieldSelectorParser constructor.
		 *
		 * @param string $selector
		 */
		public function __construct($selector) {
			$this->selector = $selector;
			$this->length = strlen($selector);
		}

		/**
		 * @return array
		 */
		public function all() {
			$all = [];

			while ($next = $this->next())
				$all = array_merge_recursive($all, $next);

			return $all;
		}

		/**
		 * @return array|null
		 */
		public function next() {
			if ($this->pos >= $this->length - 1)
				return null;

			return $this->parse($this->getNextFragment($this->selector, $this->length, $this->pos));
		}

		/**
		 * @return void
		 */
		public function reset() {
			$this->pos = 0;
		}

		/**
		 * @param $part
		 *
		 * @return array
		 */
		protected function parse($part) {
			$subPos = strpos($part, self::TOKEN_SUBFIELD_OPEN);

			if ($subPos === false)
				return [$part => true];

			$key = substr($part, 0, $subPos++);
			$fields = [];

			$subSelector = substr($part, $subPos, -1);
			$subLength = strlen($subSelector);
			$subPos = 0;

			while ($fragment = $this->getNextFragment($subSelector, $subLength, $subPos))
				if (($index = strpos($fragment, self::TOKEN_SUBFIELD_OPEN)) !== false) {
					$subfields = $this->parse($fragment);

					foreach ($subfields as $k => $v)
						$fields[$k] = $v;
				} else
					$fields[$fragment] = true;

			return [$key => $fields];
		}

		/**
		 * @param      $string
		 * @param null $length
		 * @param null $pos
		 *
		 * @return string
		 * @throws \Exception
		 */
		protected function getNextFragment($string, $length = null, &$pos = null) {
			$state = self::STATE_DATA;
			$buffer = '';

			$length = $length ?: strlen($string);
			$pos = $pos ?: 0;

			while ($pos < $length) {
				$char = $string[$pos++];

				if ($char === self::TOKEN_FIELD_SEPARATOR && $state === self::STATE_DATA)
					return $buffer;
				else if ($char === self::TOKEN_SUBFIELD_OPEN)
					$state = self::STATE_SUBFIELD;
				else if ($char === self::TOKEN_SUBFIELD_CLOSE)
					$state = self::STATE_DATA;

				$buffer .= $char;
			}

			if ($state !== self::STATE_DATA)
				throw new \Exception('Unexpected end of string');

			return $buffer;
		}
	}