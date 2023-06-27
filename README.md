# Gif Avatars

[![license](https://img.shields.io/github/license/nearata/flarum-ext-gif-avatars?style=flat)](https://github.com/Nearata/flarum-ext-gif-avatars/blob/main/UNLICENSE)
[![packagist](https://img.shields.io/packagist/v/nearata/flarum-ext-gif-avatars?style=flat)](https://packagist.org/packages/nearata/flarum-ext-gif-avatars)
[![changelog](https://img.shields.io/github/release-date/nearata/flarum-ext-gif-avatars?label=last%20release%20date)](https://github.com/Nearata/flarum-ext-gif-avatars/blob/main/CHANGELOG.md)

> Add gif avatars

## How to use

If the user has permission to use gifs, they just need to upload it.

If [gifsicle](https://github.com/kohler/gifsicle) is available globally, than the gifs will be resized to 100x100 pixels.

## Install

```sh
composer require nearata/flarum-ext-gif-avatars:"*"
```

## Update

```sh
composer update nearata/flarum-ext-gif-avatars:"*"
php flarum cache:clear
```

## Remove

```sh
composer remove nearata/flarum-ext-gif-avatars
php flarum cache:clear
```
