# Installation
```bash
$ composer require dbstudios/doze
```

# Basic Configuration
Doze only requires two things to function out of the box: a serializer, and a responder. For the serializer, Doze makes
use of the [`symfony/serializer` package](https://packagist.org/packages/symfony/serializer). You can find more
information on using it's features in their README, but a simple example can be found below.

```php
<?php
    use Symfony\Component\Serializer\Serializer;
    use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
    use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
    use Symfony\Component\Serializer\Encoder\JsonEncoder;

    $serializer = new Serializer([
        new DateTimeNormalizer(),
        new ObjectNormalizer(),
    ], [
        new JsonEncoder(),
    ]);

    $responder = new Responder($serializer);
    $response = $responder->createResponse('json', [
        'name' => 'Example',
        'someOtherField' => 'Value',
    ]);

    echo $response->getContent();

    // {"name":"Example","someOtherField":"Value"}
```

In the example, we first create a new serializer instance. The first argument to the constructor is an array of
normalizers, which the serializer uses to simplify data prior to serialization. Both `DateTimeNormalizer` and
`ObjectNormalizer` are shipped with `symfony/serializer`, and are used to normalize `DateTime` and generic objects,
respectively. The second argument is a list of supported encoder formats. Our example only supports JSON.

Next, we create a new `Responder` instance, and call it's `createResponse()` method to obtain our `Response` object,
which contains things like the headers, HTTP status code, and the response body. Please see the
[`symfony/httpfoundation` package](https://packagist.org/packages/symfony/http-foundation) for more information on the
`Response` object.

# Field Selector Notation
Sometimes, a developer using your API may only want or need a few specific fields from an object or array. To that end,
Doze implements a selector notation that can be used to limit which fields are serialized and returned.

When calling `Responder::createResponse()`, you can provide an "attributes" key, which tells the serializer which
attributes should be serialized and returned. Using the "attributes" context key is documented in the serializer package,
but Doze also supports an alternative method of building the attributes filter.

In the example below, `$responder` is the `Responder` instance we created in the previous example.
Since the fields selector functionality is intended to be exposed to the end user of your API, we're retrieving the
selector from the `$_GET` global variable.

```php
<?php
    // $_GET['fields'] = 'name,anotherField'

    use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

    $parser = new FieldSelectorParser($_GET['fields']);
    $attributes = $parser->all();

    /**
     *  [
     *      'name' => true,
     *      'anotherField' => true,
     *  ]
     */

     $response = $responder->createResponse('json', [
        'name' => 'Example',
        'someOtherField' => 'Value',
        'anotherField' => 'Another Value',
    ], null, [], [
        AbstractNormalizer::ATTRIBUTES => $attributes,
    ]);

    echo $response->getContent();

    // {"name":"Example","anotherField":"Another Value"}
```

Notice that the JSON output by `$response->getContent()` does not contain the field "someOtherField", which was not part
of the selector.

Field selectors can also contain nested selectors. In the example below, assume that `$data` has a field named
"nestedObject", which has the fields "name" and "property".

```php
<?php
    // $_GET['fields'] = 'name,nestedObject{property}';

    use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

    $parser = new FieldSelectorParser($_GET['fields']);
    $attributes = $parser->all();

    /**
     *  [
     *      'name' => true,
     *      'nestedObject' => [
     *          'property' => true,
     *      ],
     *  ]
     */

     $response = $responder->createResponse('json', $data, null, [], [
        AbstractNormalizer::ATTRIBUTES => $attributes,
    ]);

    echo $response->getContent();

    // {"name":"Example","nestedObject":{"property":"Value"}}
```

# Serializing Database Objects
Objects loaded from the database by certain libraries (such as [Doctrine](http://www.doctrine-project.org/) cannot be
cleanly serialized, since they automatically link to related objects. In some cases, serializing a database entity can
result in a huge tree of child entities being serialized as well. This causes a lot of unnecessary data to be sent across
the wire.

Doze provides a special normalizer, the `EntityNormalizer`, that is aware of such database entities (via the
`EntityInterface` interface), and will serialize ONLY the entities ID unless it is explicitly requested using
[field selectors](#field-selector-notation). To use it, any classes that are used by your DBAL must implement the
`EntityInterface`, and the `EntityNormalizer` must be in your serializer's normalizer stack above any other
normalizers that might accidentally try to serialize your objects (such as the `ObjectNormalizer` used in previous
examples).

```php
<?php
    use Symfony\Component\Serializer\Serializer;
    use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
    use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
    use Symfony\Component\Serializer\Encoder\JsonEncoder;
    use DaybreakStudios\Doze\Serializer\EntityNormalizer;

    $serializer = new Serializer([
        new DateTimeNormalizer(),
        new EntityNormalizer(),
        // EntityNormalizer must be added above less specific normalizers, like the ObjectNormalizer
        new ObjectNormalizer(),
    ], [
        new JsonEncoder(),
    ]);
```

With the extra normalizer added, any objects that implement `EntityInterface` will have ONLY their ID serialized,
instead of the entire object, and any child objects. This can be overwritten by using explicitly selecting the
entity (and, optionally, and of it's fields) using the [Field Selector Notation](#field-selector-notation).