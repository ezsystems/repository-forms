#!/bin/sh

# File for setting up system for behat testing, just like done in DemoBundle's .travis.yml

# Change local git repo to be a full one as we will reuse it for composer install below
git fetch --unshallow && git checkout -b tmp_travis_branch
export BRANCH_BUILD_DIR=$TRAVIS_BUILD_DIR
export TRAVIS_BUILD_DIR="$HOME/build/ezplatform"

# Checkout meta repo, use the branch indicated in composer.json under extra._ezplatform_branch_for_behat_tests
EZPLATFORM_BRANCH=`php -r 'echo json_decode(file_get_contents("./composer.json"))->extra->_ezplatform_branch_for_behat_tests;'`

echo "$EZPLATFORM_BRANCH"

cd "$HOME/build"

git clone --depth 1 --single-branch --branch "$EZPLATFORM_BRANCH" https://github.com/ezsystems/ezplatform.git
cd ezplatform

# Install everything needed for behat testing, using our local branch of this repo
./bin/.travis/setup_from_external_repo.sh $BRANCH_BUILD_DIR "ezsystems/repository-forms:dev-tmp_travis_branch as 2.0"
