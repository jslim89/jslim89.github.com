#!/bin/bash

## Edit here

# The path please don't put any quote (" or ') to wrap it
project_path=$HOME/path/to/project
project=$project_path/Warranty\ Reminder.xcodeproj
# The scheme in Xcode
scheme="Warranty Reminder"
# Follow exactly what is in the built setting in Xcode
provision_profile="iOSTeam Provisioning Profile: com.jslim89.Warranty-Reminder"

# Get the version from the plist, ref: http://stackoverflow.com/questions/4328501/how-to-read-plist-information-bundle-id-from-a-shell-script/4330902#4330902
version=$(/usr/libexec/PlistBuddy -c "Print :CFBundleShortVersionString" "$project_path/Warranty\ Reminder/Warranty\ Reminder-Info.plist")

# just the output file name or path without extension
output="$HOME/Desktop/warranty-reminder-v$version"


## Remain unchange
tmp_archive="./proj.xcarchive"

xcodebuild clean -project "$project" -configuration Release -alltargets

xcodebuild archive -project "$project" -scheme "$scheme" -archivePath "$tmp_archive"

xcodebuild -exportArchive -archivePath "$tmp_archive" -exportPath "$output" -exportFormat ipa -exportProvisioningProfile "$provision_profile"

rm -rf "$tmp_archive"
