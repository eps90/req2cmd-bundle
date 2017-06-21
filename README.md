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
I hope that things like custom command bus, customer serializer or event completely framework-agnostic code
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
   extractor:
      service_id: app.extractor.my_extractor
# ...
```
> **Note:** Defining string value to `req2cmd.extractor` config property
 is only available for built-in extractors. 
 For now only `serializer` and `jms_serializer` are allowed.

### ... and I want other command bus as well!

It'll be available soon as well, stay tuned.

## Testing and contributing

This project is covered with PHPUnit tests. To run them, type:

```bash
bin/phpunit
```
