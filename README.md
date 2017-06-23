# Req2Cmd Bundle

Extract command from a HTTP request and send it to the [Tactician command bus](http://tactician.thephpleague.com/).  

[![Latest Stable Version](https://poser.pugx.org/eps90/req2cmd-bundle/v/stable)](https://packagist.org/packages/eps90/req2cmd-bundle)
[![Latest Unstable Version](https://poser.pugx.org/eps90/req2cmd-bundle/v/unstable)](https://packagist.org/packages/eps90/req2cmd-bundle)
[![License](https://poser.pugx.org/eps90/req2cmd-bundle/license)](https://packagist.org/packages/eps90/req2cmd-bundle)

[![Build Status](https://travis-ci.org/eps90/req2cmd-bundle.svg?branch=master)](https://travis-ci.org/eps90/req2cmd-bundle)
[![Coverage Status](https://coveralls.io/repos/github/eps90/req2cmd-bundle/badge.svg?branch=master)](https://coveralls.io/github/eps90/req2cmd-bundle?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/eps90/req2cmd-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/eps90/req2cmd-bundle/?branch=master)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/413e7b41b7874d818266ac668f4edd92)](https://www.codacy.com/app/eps90/req2cmd-bundle?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=eps90/req2cmd-bundle&amp;utm_campaign=Badge_Grade)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/ecc340e9-deab-47da-928c-b30c384df654/big.png)](https://insight.sensiolabs.com/projects/ecc340e9-deab-47da-928c-b30c384df654)


## Motivation

Recently I've been writing some project with framework-agnostic code
with [CQRS](https://martinfowler.com/bliki/CQRS.html) approach 
so I could have all use cases in separate classes written in clean
and readable way. When I started to integrate it with [Symfony](http://symfony.com) framework
I've noticed that each controller's action looks the same: create command from request,
send to the command bus and return `Response` from action.

I've created this library to facilitate converting requests to commands
and automatically sending them to the [Tactician](http://tactician.thephpleague.com/) command bus.
Thanks to Symfony Router component and Symfony Event Dispatcher with kernel events
I was able to recognize command from route parameters and convert to the command instance.

This bundle **works** but still needs a lot of work to work in each case. 
I hope that things like custom command bus or even completely framework-agnostic code
will be available soon.
Every contribution is welcome!

## Requirements

* **PHP** 7.1+
* **Symfony Framework Bundle** (or Symfony Standard Edition) - 2.3+|3.0+
* **Tactician bundle** 0.4+

## Installation

**Step 1:** Open a command console, enter your project's root director 
and run following command to install the package with Composer:

```bash
composer require eps90/req2cmd-bundle
```

**Step 2:** Enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file:

```php
<?php

// ...
class AppKernel extends Kernel
{
    public function registerBundles(): array
    {
        $bundles = [
            // ...
            new Eps\Req2CmdBundle\Req2CmdBundle(),
            // ...
        ];
        
        // ...
    }
    
    // ...
}
```

## Usage

(Documentation in progress)

### Converting a route to a command
This bundle uses the capabilities of [Symfony Router](https://symfony.com/doc/current/routing.html)
to match a route with configure command. In the happy path, all you need to do is to set
a `_command_class` parameter in your route:

```yml
app.add_post:
  path: /add_post.{_format}
  methods: ['POST']
  defaults:
    _command_class: AppBundle\Command\AddPostCommand
    _format: ~
```

In such case, an event listener will try to convert a request contents to a command instance
with `CommandExtractor` (the default extractor is the Symfony Serializer).
The result command instance will be saved as `_command` argument in the request.

```php
<?php

// ...

final class PostController
{
    // ...
    public function addPostAction(Request $request): Response
    {
        // ...
        $command = $request->attributes->get('_command');
        // ...
    }
}
```

The only requirement is to provide a requested format (with `Request::setRequestFormat`) before the `ExtractCommandFromRequestListener` is fired.
This can be done wih already available bundles, like [FOSRestBundle](https://github.com/FriendsOfSymfony/FOSRestBundle) 
but I hope that such listener will be available soon in this bundle as well.

### Action!

If you won't add a `_controller` parameter to the route, your request will be automatically sent
to `ApiResponderAction` which is responsible for extracting a command from a request and sending it to the command bus.
Moreover, regarding the method the request has been send with, it responds with proper status code.
For example, for successful `POST` request you can expect 201 status code (201: Created).

### Custom controller

Of course, you can use your own controller, with standard `_controller` parameter.
The listener from this bundle won't override this param if it's alreade defined.

### Deserialize a command

If your command is complex and uses some nested types, default Symfony Serializer
probably won't be able to deserialize a request to your command.

This bundle comes with a denormalizer which looks up for `DeserializableCommandInterface` implementations
and calls the `fromArray` constructor on it.

```php
<?php

use Eps\Req2CmdBundle\Command\DeserializableCommandInterface;

final class AddPost implements DeserializableCommandInterface
{
    // ...
    
    public function __construct(PostId $postId, PostContents $contents) 
    {
        $this->postId = $postId;
        $this->contents = $contents;
    }
    
    // ... getters
    
    public static function fromArray(array $commandProps): self 
    {
        return new self(
            new PostId($commandProps['id']),
            new PostContents(
                $commandProps['title'],
                $commandProps['author'],
                $commandProps['contents']
            )
         );
    }
}
```

Then your command can seamlessly be deserialized with a `CommandExtractor`.
Feel free to register your own denormalizer.

You can also set a `JMSSerializerCommandExtractor` as your extractor and use handy class mappings for deserialization.

```yaml
# src/AppBundle/Resources/config/jms_serializer/Command.AddPost.yml
AppBundle\Command\AddPost:
  properties:
    postId:
      type: AppBundle\Identity\PostId
    postContents:
      type: AppBundle\ValueObject\PostContents
```
```yaml
# app/config.yml
# ...
req2cmd:
  extractor: jms_serializer
# ...
```

### Attaching path parameters to a command

You can attach route parameters to command deserialization like it was sent from a client.
Let's say you have a route mapped to a command like the following:

```yaml
app.update_post_title:
  path: /posts/{id}.{_format}
  methods: ['PUT']
  defaults:
    _command_class: AppBundle\Command\UpdatePostTitle
```

And you have that command that looks like that:

```php
<?php

final class UpdatePostTitle
{
    private $postId;
    private $postTitle;
    
    public function __construct(int $postId, string $postTitle) 
    {
        $this->postId = $postId;
        $this->postTitle = $postTitle;
    }
    
    // ...
}
```

As you can see, the `UpdatePost` command requires an id and some string 
that should allow to update a post title.

That command, to be serialized correctly, needs both parameters in request's contents. 
Of course, you can send following request to send your command to the event bus:
 
```
PUT http://example.com/api/posts/4234.json
{
    "id": 4234,
    "title": "Updated title"
}

```

As you can see, the `id` property exists in a path and in a request body.
To remove this duplication you can point a **route parameter**
to be included in deserialization:

```yaml
app.update_post_title:
  path: /posts/{id}.{_format}
  defaults:
    _command_class: AppBundle\Command\UpdatePostTitle
    _command_properties:
      path:
        id: ~
```

Then, the `id` from route will be passed on, like it's been a part of request body,
and will create your command properly. Then your request may look like that:

```
PUT http://example.com/api/posts/4234.json
{
    "title": "Updated title"
}

```

And everything will work as expected.

> By **route parameters** I mean **all route parameters** so if you want to attach,
for example, a `_format` (yep, I know, a stupid example), you can do it in the same way.

#### Change route parameters names

You may want to change a parameter name before it goes to the extractor.
Given the example above, the serializer will probably need a `post_id` instead
of `id` in request content. The name can be changed by passing a value to parameter name
in route definition:

```yaml
app.update_post_title:
  path: /posts/{id}.{_format}
  defaults:
    _command_class: AppBundle\Command\UpdatePostTitle
    _command_properties:
      path:
        id: post_id
```

Then the following code will work:


```php
<?php

use Eps\Req2CmdBundle\Command\DeserializableCommandInterface;

final class UpdatePostTitle implements DeserializableCommandInterface
{
    // ...
    
    public static function fromArray(array $commandProps): self 
    {
        return new self($commandProps['post_id'], $commandProps['title']); 
    }   
}
```

#### Required route parameters

A `PathParamsMapper` can recognize whether configure parameter should be required and not empty.
To allow it, prepend a parameter name with an exclamation mark:
 
```
app.update_post_title:
  path: /posts/{id}.{_format}
  defaults:
    _format: ~
    _command_class: AppBundle\Command\UpdatePostTitle
    _command_properties:
      path:
        !_format: requested_format
```

In this case, when `_format` parameter will equal `null`, the mapper will throw
a `ParamMapperException`.

#### Registering custom parameter mappers

The default parameter mapper is the `PathParamsMapper` class instance and it's responsible only for
extracting only route parameters. Of course you can feel free to register your own mapper,
by implement the `ParamMapperInterface`.

When you're done, register it as a service and add the `req2cmd.param_mapper` tag.
Optionally, you can set a priority to make sure that this mapper will be executed earlier.
The higher priority is, the more important the service is.

```yaml
services:
# ...
  app.param_mapper.my_awesome_mapper:
    class: AppBundle\ParamMapper\MyAwesomeMapper
    tags:
      - { name: 'req2cmd.param_mapper', priority: 128 }
```

### But I want to use different extractor!

Sure, why not! 
You need to create a class implementing the `CommandExtractorInterface` interface. 
This interface contains only one method, `extractFromRequest`, where you can access a `Request` and a command class.
For example:

```php
<?php

use Eps\Req2CmdBundle\CommandExtractor\CommandExtractorInterface;
// ...

class DummyExtractor implements CommandExtractorInterface
{
    public function extractorFromRequest(Request $request, string $commandName)
    {
        // get the requested format from the Request object 
        if ($request->getRequestFormat() === 'json') {
            // decode contents
            $contents = json_decode($request->getContents(), true); 
        }
        
        // and return command instance
        return new $commandName($contents); 
    }
}
```

Then, register this service in service mappings:

```yaml
services:
# ...
  app.extractor.my_extractor: 
    class: AppBundle\Extractor\DummyExtractor
```

And adapt project configuration by setting `extractor.service_id` value:

```yaml
# ...
req2cmd:
  # ...
   extractor:
      service_id: app.extractor.my_extractor
# ...
```
> **Note:** Defining string value to `req2cmd.extractor` config property
 is only available for built-in extractors. 
 For now only `serializer` and `jms_serializer` are allowed.

### ... and I want other command bus as well!

You can use whatever command bus you want.
The only condition is you need to write an adapter implementing `CommandBusInterface`.

Then you can register it as a service and adapt configuration:

```yaml
# app/config/config.yml
# ...
req2cmd:
  # ...
  command_bus:
    service_id: app.command_bus.my_command_bus
  # ...
# ...
```

> **Note:** Tactician is the default command bus so you don't have to configure
it manually. Actually, the following configuration is equivalent to missing one:

```yaml
# ...
req2cmd:
  # Short version:
  command_bus: tactician
  # Verbose version:
  command_bus:
    service_id: eps.req2cmd.command_bus.tactician
    name: default
# ...
```

### Configuring command bus

The default command bus is [Tactician command bus](http://tactician.thephpleague.com/)
which allows you to declare several command buses adapted to your needs.
Without touching the configuration, this bundle uses `tactician.commandbus.default` command bus
which is sufficient for most cases. However, if you need to set different command bus name,
you can do it by passing a _name_ to configuration:

```yaml
# app/config/config.yml
# ...
req2cmd:
  # ...
  command_bus:
    name: 'queued'
  # ...
```

In such case the `tactician.commandbus.queued` will be used.

## Exceptions

All exceptions in this bundle implement the `Req2CmdExceptionInterface`.
Currently, the following exceptions are configured:

* `ParamMapperException`
    * `::noParamFound` (code **101**) - when required property has not been found in a request
    * `::paramEmpty` (code **102**) - when required property is found but it's empty

## Testing and contributing

This project is covered with PHPUnit tests. To run them, type:

```bash
bin/phpunit
```
