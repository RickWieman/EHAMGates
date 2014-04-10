#!/bin/bash

cd tests

php phpunit.phar --coverage-html ../tests_results .
