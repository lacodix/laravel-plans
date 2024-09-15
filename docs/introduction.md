---
title: Introduction
weight: 1
---

This package gives you the ability to manage subscriptions for your laravel based SaaS.
You can create Plans with Features and subscribe to them. Additionally you get some support
functions for a subsequent billing system of invoice creation package.

## What it does

- Manage all **Plans** and **Addons** of your SaaS where users can subscribe to.
- **Subscribe** to one or more plans with different billing intervals.
- Manage optional **features**, if you need it. You can also stick with plans and just check for a subscribed plan.
- Offer **countable** and **uncountable** features. Use uncountable features to just enable/disable a functionality. Use
  countable features, for things like tokens, credits, and others. Attach different values of the feature to different
  plans, including "unlimited". Auto reset the values after given intervals.
- **Consume** Features as long as some amount is available. Split up usage on different plans, depending on the order of
  the plans.
- **Translate** your plans and features. Identify both by slug in your app, but offer it to your users localized, powered
  by [spatie/laravel-translatable](https://github.com/spatie/laravel-translatable)
- **Order** your plans, features (for visualisation to your users) and subscriptions (to keep control over usage order)
- Allow **meta data** in plans and subscriptions. The usage of meta data is up to you. In some usecase we take meta data
  to save currency or additional price information. This information can be used by a subsequent billing system.

## What it doesn't

- **Billing**. We don't create invoices or keep track on bills. Use the invoice service of your choice, doesn't matter which.
  Stripe, PayPal, your own software and any other. You just get events from this package, when subscriptions are created
  or renewed, and this can be used to trigger invoices. You are also free to wait for payment before you create the
  subscription or the other way round.
- **Pricing**. Yes there is a price column in the plan model, that can be used for visualisation. You also can use meta data
  for additional price information like we do it in some examples. But you don't need to use it. You can keep your prices
  in your billing system or save it separate from the plans. But indeed, if you use the price-column you can also get
  calculated prices for partial subscription intervals.

## We have badges

[![Latest Version on Packagist](https://img.shields.io/packagist/v/lacodix/laravel-plans.svg?style=flat-square)](https://packagist.org/packages/lacodix/laravel-plans) 
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/lacodix/laravel-plans/test.yaml?branch=master&label=tests&style=flat-square)](https://github.com/lacodix/laravel-plans/actions?query=workflow%3Atest+branch%3Amaster)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/lacodix/laravel-plans/style.yaml?branch=master&label=code%20style&style=flat-square)](https://github.com/lacodix/laravel-plans/actions?query=workflow%3Astyle+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/lacodix/laravel-plans.svg?style=flat-square)](https://packagist.org/packages/lacodix/laravel-plans)
