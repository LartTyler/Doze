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

		public function next() {
			if ($this->pos >= $this->length - 1)
				return null;

			return $this->parse($this->getNextFragment($this->selector, $this->length, $this->pos));
		}

		/**
		 * @param $part
		 *
		 * @return FieldSelector
		 */
		protected function parse($part) {
			$subPos = strpos($part, self::TOKEN_SUBFIELD_OPEN);

			if ($subPos === false)
				return new FieldSelector('', [$part]);

			$key = substr($part, 0, $subPos++);
			$fields = [];

			$subSelector = substr($part, $subPos, -1);
			$subLength = strlen($subSelector);
			$subPos = 0;

			while ($fragment = $this->getNextFragment($subSelector, $subLength, $subPos)) {
				if (($index = strpos($fragment, self::TOKEN_SUBFIELD_OPEN)) !== false) {
					$prefix = substr($fragment, 0, $index);

					$fields = array_merge($fields, array_map(function($subfield) use ($prefix) {
						return sprintf('%s.%s', $prefix, $subfield);
					}, $this->parse($fragment)->getSubfields()));
				} else
					$fields[] = $fragment;
			}

			return new FieldSelector($key, $fields);
		}

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