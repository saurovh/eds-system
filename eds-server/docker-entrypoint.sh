#!/usr/bin/env bash
# shellcheck shell=bash
set -e

COMPOSER_DISCARD_CHANGES=true composer install -n

exec "$@"
