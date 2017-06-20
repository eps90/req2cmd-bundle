# Request2CommandBusBundle
Converts Symfony HTTP request to command and sends to the command bus

[![Build Status](https://travis-ci.org/eps90/req2cmd-bundle.svg?branch=master)](https://travis-ci.org/eps90/req2cmd-bundle)
[![Coverage Status](https://coveralls.io/repos/github/eps90/req2cmd-bundle/badge.svg?branch=master)](https://coveralls.io/github/eps90/req2cmd-bundle?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/eps90/req2cmd-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/eps90/req2cmd-bundle/?branch=master)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/413e7b41b7874d818266ac668f4edd92)](https://www.codacy.com/app/eps90/req2cmd-bundle?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=eps90/req2cmd-bundle&amp;utm_campaign=Badge_Grade)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/ecc340e9-deab-47da-928c-b30c384df654/big.png)](https://insight.sensiolabs.com/projects/ecc340e9-deab-47da-928c-b30c384df654)


## Motivation

Recently I've been writing some framework-agnostic project 
which uses [CQRS](https://martinfowler.com/bliki/CQRS.html) approach.
With that I can have all use cases in separate classes, written in clean
and readable way. When I started to integrate it with [Symfony](http://symfony.com) framework,
I've noticed that each controller's action looks the same: create command from request,
send to the command bus, return `Response` from action.

I've created this library to facilitate converting requests to commands
and automatically sending them to [Tactician](http://tactician.thephpleague.com/) command bus.
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
composer require [complete package name here] #todo 
```

**Step 2:** Enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file:

```php
<?php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
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
with `CommandExtractor` (currently the default extractor is the Symfony Serializer).
The result command instance will be saved as `_command` argument in the request.

```php
<?php

// ...

class PostController
{
    // ...
    public function addPostAction(Request $request)
    {
        // ...
        $command = $request->attributes->get('_command');
        // ...
    }
}
```

### Action!

If you won't add a `_controller` parameter to the route, your request will be automatically sent
to `ApiResponderAction` which is responsible for extracting a command from a request and sending it to the command bus.
Moreover, regarding the method the request has been send with, it responds with proper status code.
For example, for successful `POST` request you can expect 201 status code (201: Created).

### Custom controller

Of course, you can use your own controller, with standard parameter, `_controller`.
The listener from this bundle won't override this param if it's alreade defined.

### Deserialize a command

Probably you won't need this but if your command is complex and uses custom nested types, default Symfony Serializer
won't have no clue how to deserialize a request to your command.

This bundle comes with denormalizer which looks up for `DeserializableCommandInterface` implementations
and calls a named constructor on it.

The only requirement is to provide (somehow) a requested format before this listener is fired.
This can be done wih already available bundles. I hope it'll be available soon here as well.

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

Then your command can seamlessly be deserialize with a `CommandExtractor`.
Feel free to register your own denormalizer.

### But I want to use different serializer!

Currently it's not possible, but I'll hope it'll get fixed soon.
Code is ready for other `CommandExtractors` but the service mappings aren't.

### ... and I want other command bus as well!

It'll be available soon as well, stay tuned.

## Testing and contributing

This project is covered with PHPUnit tests. To run them, type:

```bash
bin/phpunit
```
