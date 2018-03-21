#!/usr/bin/env bash

# Delete old classes:
rm -rf generated-classes/Gtd/Propel/Base
rm -rf generated-classes/Gtd/Propel/Map
rm -rf generated-conf
rm -rf generated-sql
 
# Generate classes and database:
vendor/bin/propel sql:build
vendor/bin/propel model:build
vendor/bin/propel config:convert
vendor/bin/propel sql:insert