#!/bin/bash

## Edit here

# The path please don't put any quote (" or ') to wrap it
project=$HOME/path/to/Warranty\ Reminder.xcodeproj
# The scheme in Xcode
scheme="Warranty Reminder"
# Follow exactly what is in the built setting in Xcode
provision_profile="iOSTeam Provisioning Profile: com.jslim89.Warranty-Reminder"
# just the output file name or path without extension
output="warranty-reminder"


## Remain unchange
tmp_archive="./proj.xcarchive"

xcodebuild clean -project "$project" -configuration Release -alltargets

xcodebuild archive -project "$project" -scheme "$scheme" -archivePath "$tmp_archive"

xcodebuild -exportArchive -archivePath "$tmp_archive" -exportPath "$output" -exportFormat ipa -exportProvisioningProfile "$provision_profile"

rm -rf "$tmp_archive"
