<?php
	use DaybreakStudios\Doze\Responder;
	use DaybreakStudios\Doze\Serializer\EntityNormalizer;
	use DaybreakStudios\Doze\Serializer\FieldSelectorParser;
	use Symfony\Component\Serializer\Encoder\JsonEncoder;
	use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
	use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
	use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
	use Symfony\Component\Serializer\Serializer;

	require __DIR__ . '/../vendor/autoload.php';
	require __DIR__ . '/TestEntity.php';

	$responder = new Responder(new Serializer([
		new DateTimeNormalizer(),
		new EntityNormalizer(),
		new ObjectNormalizer(),
	], [
		new JsonEncoder(),
	]));

	$fieldsInPayload = 'id,name,otherEntity';
	$parser = new FieldSelectorParser($fieldsInPayload);

	$fields = $parser->all();

	$response = $responder->createResponse('json', new TestEntity(), null, [], [
		AbstractNormalizer::ATTRIBUTES => $fields,
	]);

	echo $response->getContent() . PHP_EOL;

	function cleanExplode($delim, $string) {
		return array_filter(array_map(function($item) {
			return trim($item);
		}, explode($delim, $string)));
	}