# Request2CommandBusBundle
Converts Symfony HTTP request to command and sends to the command bus

[![Build Status](https://travis-ci.org/eps90/request-to-command-bus-bundle.svg?branch=master)](https://travis-ci.org/eps90/request-to-command-bus-bundle)
[![Coverage Status](https://coveralls.io/repos/github/eps90/request-to-command-bus-bundle/badge.svg?branch=master)](https://coveralls.io/github/eps90/request-to-command-bus-bundle?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/eps90/request-to-command-bus-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/eps90/request-to-command-bus-bundle/?branch=master)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/6e6b361b00a145168f3ebfbc06c7ca08)](https://www.codacy.com/app/eps90/request-to-command-bus-bundle?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=eps90/request-to-command-bus-bundle&amp;utm_campaign=Badge_Grade)

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

Install the package with following command:

```bash
composer require [complete package name here] #todo 
```

And add a bundle to your `AppKernel`: (todo)

## Usage (todo)

## Testing (todo)

## Things to do in the nearest future
