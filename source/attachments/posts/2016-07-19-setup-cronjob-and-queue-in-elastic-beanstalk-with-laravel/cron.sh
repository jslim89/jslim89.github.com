#!/bin/bash
#
# Author: Js Lim (jslim@webqlo.com)
# Version: 2016-06-21
#
# install crontab
#

if [ "${INS_CRONTAB}" == "enable" ]; then
    # create a home folder for non-login user "webapp", otherwise it will throw error
    mkdir /home/webapp
    chown webapp:webapp /home/webapp

    # remove existing crontab
    crontab -r -u webapp
    crontab .ebextensions/crontab -u webapp
fi
