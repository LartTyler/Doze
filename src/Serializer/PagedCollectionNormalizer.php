<?php
	namespace DaybreakStudios\Doze\Serializer;

	use DaybreakStudios\Doze\Entity\EntityInterface;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Criteria;
	use Doctrine\Common\Collections\Selectable;
	use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
	use Symfony\Component\Serializer\SerializerAwareInterface;
	use Symfony\Component\Serializer\SerializerAwareTrait;

	class PagedCollectionNormalizer implements NormalizerInterface, SerializerAwareInterface {
		use SerializerAwareTrait;

		const CONTEXT_URL_TEMPLATE = 'doze.url_template';

		/**
		 * @var int
		 */
		protected $itemCount;

		/**
		 * @var callable
		 */
		protected $idEncoder;

		/**
		 * @var string
		 */
		protected $previousKey = 'previous';

		/**
		 * @var string
		 */
		protected $nextKey = 'next';

		/**
		 * @var bool
		 */
		protected $includeCursors = true;

		/**
		 * @var string
		 */
		protected $cursorsKey = 'cursors';

		/**
		 * @var string
		 */
		protected $beforeCursorKey = 'before';

		/**
		 * @var string
		 */
		protected $afterCursorKey = 'after';

		/**
		 * @var string
		 */
		protected $beforeCursorEmptyValue = '0';

		/**
		 * @var string
		 */
		protected $afterCursorEmptyValue = '0';

		/**
		 * PagedCollectionNormalizer constructor.
		 *
		 * @param int           $itemCount
		 * @param callable|null $idEncoder
		 */
		public function __construct($itemCount, callable $idEncoder = null) {
			$this->itemCount = $itemCount;
			$this->idEncoder = $idEncoder ?: function($id) {
				return $id;
			};
		}

		/**
		 * @return int
		 */
		public function getItemCount() {
			return $this->itemCount;
		}

		/**
		 * @param int $itemCount
		 *
		 * @return $this
		 */
		public function setItemCount($itemCount) {
			$this->itemCount = $itemCount;

			return $this;
		}

		/**
		 * @return callable
		 */
		public function getIdEncoder() {
			return $this->idEncoder;
		}

		/**
		 * @param callable $idEncoder
		 *
		 * @return $this
		 */
		public function setIdEncoder(callable $idEncoder) {
			$this->idEncoder = $idEncoder;

			return $this;
		}

		/**
		 * @return string
		 */
		public function getPreviousKey() {
			return $this->previousKey;
		}

		/**
		 * @param string $previousKey
		 *
		 * @return $this
		 */
		public function setPreviousKey($previousKey) {
			$this->previousKey = $previousKey;

			return $this;
		}

		/**
		 * @return string
		 */
		public function getNextKey() {
			return $this->nextKey;
		}

		/**
		 * @param string $nextKey
		 *
		 * @return $this
		 */
		public function setNextKey($nextKey) {
			$this->nextKey = $nextKey;

			return $this;
		}

		/**
		 * @return bool
		 */
		public function getIncludeCursors() {
			return $this->includeCursors;
		}

		/**
		 * @param bool $includeCursors
		 *
		 * @return $this
		 */
		public function setIncludeCursors($includeCursors) {
			$this->includeCursors = $includeCursors;

			return $this;
		}

		/**
		 * @return string
		 */
		public function getCursorsKey() {
			return $this->cursorsKey;
		}

		/**
		 * @param string $cursorsKey
		 *
		 * @return $this
		 */
		public function setCursorsKey($cursorsKey) {
			$this->cursorsKey = $cursorsKey;

			return $this;
		}

		/**
		 * @return string
		 */
		public function getBeforeCursorKey() {
			return $this->beforeCursorKey;
		}

		/**
		 * @param string $beforeCursorKey
		 *
		 * @return $this
		 */
		public function setBeforeCursorKey($beforeCursorKey) {
			$this->beforeCursorKey = $beforeCursorKey;

			return $this;
		}

		/**
		 * @return string
		 */
		public function getAfterCursorKey() {
			return $this->afterCursorKey;
		}

		/**
		 * @param string $afterCursorKey
		 *
		 * @return $this
		 */
		public function setAfterCursorKey($afterCursorKey) {
			$this->afterCursorKey = $afterCursorKey;

			return $this;
		}

		/**
		 * @return string
		 */
		public function getBeforeCursorEmptyValue() {
			return $this->beforeCursorEmptyValue;
		}

		/**
		 * @param string $beforeCursorEmptyValue
		 *
		 * @return $this
		 */
		public function setBeforeCursorEmptyValue($beforeCursorEmptyValue) {
			$this->beforeCursorEmptyValue = $beforeCursorEmptyValue;

			return $this;
		}

		/**
		 * @return string
		 */
		public function getAfterCursorEmptyValue() {
			return $this->afterCursorEmptyValue;
		}

		/**
		 * @param string $afterCursorEmptyValue
		 *
		 * @return $this
		 */
		public function setAfterCursorEmptyValue($afterCursorEmptyValue) {
			$this->afterCursorEmptyValue = $afterCursorEmptyValue;

			return $this;
		}

		/**
		 * {@inheritdoc}
		 */
		public function supportsNormalization($data, $format = null) {
			return is_object($data) && $data instanceof Selectable;
		}

		/**
		 * {@inheritdoc}
		 */
		public function normalize($object, $format = null, array $context = []) {
			if (!isset($context[static::CONTEXT_URL_TEMPLATE]))
				throw new \Exception(static::class . ' requires ' . static::CONTEXT_URL_TEMPLATE . ' to be set in the' .
					'serializer context');

			$serializer = $this->serializer;

			if (!($serializer instanceof NormalizerInterface))
				throw new \LogicException(sprintf('Cannot normalize "%s" because the injected serializer ' .
					'is not a normalizer', get_class($object)));

			$criteria = Criteria::create()
				->setMaxResults($this->getItemCount())
				->setFirstResult(0);

			/** @var Selectable|Collection|EntityInterface[] $object */
			$items = $object->matching($criteria);

			$this->verifyContainsEntitiesOnly($items);

			// Ex: https://example.com/api/thing, https://example.com/api/thing?queryVar=1
			$url = $context[static::CONTEXT_URL_TEMPLATE];
			$originalQuery = parse_url($url, PHP_URL_QUERY);

			if ($originalQuery === false)
				throw new \Exception('The URL ' . $url . ' is malformed');

			if ($originalQuery) {
				$url = str_replace('?' . $originalQuery, '', $url);

				parse_str($originalQuery, $query);
			} else
				$query = [];

			if ($fragment = parse_url($url, PHP_URL_FRAGMENT))
				$url = str_replace('#' . $fragment, '', $url);
			else
				$fragment = '';

			if (!$items->count()) {
				$beforeId = $this->getBeforeCursorEmptyValue();
				$afterId = $this->getAfterCursorEmptyValue();
			} else {
				$beforeId = $items->first()->getId();
				$afterId = $items->last()->getId();
			}

			$beforeId = call_user_func($this->getIdEncoder(), $beforeId);
			$afterId = call_user_func($this->getIdEncoder(), $afterId);

			$paging = [
				$this->getPreviousKey() => $this->assembleUrl($url, [
					$this->getBeforeCursorKey() => $beforeId,
				] + $query, $fragment),
				$this->getNextKey() => $this->assembleUrl($url, [
					$this->getAfterCursorKey() => $afterId,
				] + $query, $fragment),
			];

			if ($this->getIncludeCursors())
				$paging += [
					$this->getCursorsKey() => [
						$this->getBeforeCursorKey() => $beforeId,
						$this->getAfterCursorKey() => $afterId,
					],
				];

			return [
				'results' => array_map(function($item) use ($serializer, $format, $context) {
					return $serializer->normalize($item, $format, $context);
				}, $items->getValues()),
				'paging' => $paging,
			];
		}

		/**
		 * @param string $baseUrl
		 * @param array  $query
		 * @param string $fragment
		 *
		 * @return string
		 */
		protected function assembleUrl($baseUrl, array $query = [], $fragment = '') {
			if ($query)
				$baseUrl .= '?' . http_build_query($query);

			if ($fragment)
				$baseUrl .= '#' . $fragment;

			return $baseUrl;
		}

		/**
		 * @param Collection $collection
		 *
		 * @return void
		 */
		protected function verifyContainsEntitiesOnly(Collection $collection) {
			$check = $collection->forAll(function($key, $element) {
				return $element instanceof EntityInterface;
			});

			if (!$check)
				throw new \InvalidArgumentException('Items in the collection must all implement ' .
					EntityInterface::class);
		}
	}