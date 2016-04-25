#!/bin/bash

### CONFIG ###
DEPLOY_PRODUCTION="sh deploy.sh"
DEPLOY_STAGING=""

BRANCH_PRODUCTION="master"
BRANCH_STAGING="dev"

### END CONFIG ###

### BEFORE BUILD/DEPLOY ####

### DO NOT EDIT PAST THiS LINE - UNLESS YOU ARE AWESOME ###
rc=0

# ONLY DEPLOY IF THIS IS NOT A PULL REQUEST
if [[ $TRAVIS_PULL_REQUEST = 'false' ]] ; then

  if [[ $TRAVIS_BRANCH = $BRANCH_PRODUCTION ]] ; then
    eval $DEPLOY_PRODUCTION
    rc=$?
  fi

  if [[ $TRAVIS_BRANCH = $BRANCH_STAGING ]] ; then
    eval $DEPLOY_STAGING
    rc=$?
  fi

fi

if [[ $rc != 0 ]] ; then
    exit $rc
fi

exit 0