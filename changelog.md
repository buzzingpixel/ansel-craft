# Ansel for Craft Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## 2.1.3 - 2019-08-24
### Changed
- Updated composer requirements to allow for Craft 3.2.0

## 2.1.2 - 2019-05-23
### Fixed
- Fixed an issue on field save where an image marked for deletion would still increment image position and whether image should be disabled or not
- Fixed a JS bug where deleting an image from a field would not reset order or whether images were displayed as over limit and therefore disabled

## 2.1.1 - 2019-03-18
### Fixed
- Fixed an issue with a migration from the previous update

## 2.1.0 - 2019-01-29
### Fixed
- Fixed incompatibility with Craft 3.1.x

## 2.0.0 - 2018-04-04
### Changed
- BREAKING: Ansel for Craft 2.0.0 drops support for Craft 2.x entirely (is that confusing? Sorry, Ansel 1 supported Craft 2, Ansel 2 supports Craft 3. So goes life). Ansel 2.0.0 is compatible exclusively with Craft 3 and has full composer support.
- BREAKING: The entire under-the-hood API is new/changed/different. If you happened to be doing any usage of Ansel under-the-hood, I'm afraid you'll need to re-write your code to use Ansel's new services and methods.
- BREAKING: A few things about templating have changed subtly to match the Craft 3 paradigm. Please read over the documentation.
### Added
- The user interface is entirely new and takes up less room while still providing all the same basic features as Ansel for Craft 1.x.
- The new user interface is now 100% more mobile friendly.
- Ansel now utilizes Craft 3's native focal points by allowing you to set the focal point on the cropped image.
- When adding/cropping images in the field interface, Ansel will now work on image manipulations in the background via ajax while you work on content entry. In most cases, the images will all be pre-manipulated before you ever click save on your entry or element. What this means in real world terms is that you will do less waiting after clicking the save button because Ansel will have already done the heavy lifting of image cropping and manipulation by the time you save. It also means the save process should be less error prone since Ansel will now manipulate one image at a time via AJAX, giving less chance for server timeouts and memory errors.
- Ansel now supports live preview! (as best it can).
- Did I mention that this is a complete re-write of Ansel for Craft? It was a lot of work. But it was well worth it and I think you'll love it

## 1.0.7 - 2017-12-02
### Fixed
- Fixed a bug where a headless attempt to save an Ansel field would result in an error because no user is defined for the Ansel Image row
- Fixed an issue (another one) that could happen in some environments where glob returns false when no files present in directory

## 1.0.6 - 2017-07-03
### Fixed
- Fixed an issue where Ansel’s cache clean up routine may produce PHP errors in some environments

## 1.0.5 - 2017-06-24
### Added
- Improved minimum image dimensions not met message with the required image dimensions

### Fixed
- Fixed a bug where using the Craft file chooser would bypass minimum image dimensions

## 1.0.4 - 2017-06-21
### Fixed
- Fixed an issue where Ansel’s field content column was not appropriately large enough and would thrown an error “field too long for column” ([Issue #30](https://buzzingpixel.com/support/issue/30))

## 1.0.3 - 2017-05-29
### Fixed
- Fixed an issue where English translations would not be the fallback for missing lang

## 1.0.2 - 2017-05-09
### Fixed
- Fixed a bug where asset source paths that had environment variables would cause Ansel to fail to save images
- Fixed a bug where the source ID select value for sources in field settings might be wrong

## 1.0.1 - 2017-04-18
### Fixed

- Because Ansel was using Source IDs and not Folder IDs (sorry, a bit technical), assets may have ended up in the wrong directories and things may generally not be stored properly. This being the case, you may need to clean up some assets being in wrong directories. Please accept my apologies.
- Fixed issues where Ansel was not using the correct Asset location IDs (Technical jargon: Asset locations source IDs were being used instead of Folder IDs. Facepalm.gif) ([Issue #11](https://buzzingpixel.com/support/issue/11))
Fixed an issue where Ansel could not properly save or work with files from Craft Assets Cloud locations with expires settings set ([Issue #11](https://buzzingpixel.com/support/issue/11))

## 1.0.0 - 2017-03-30
### Added

- This is the initial release of Ansel for Craft CMS.
- Ansel lets you create beautiful images, the right size every time in a way that’s friendly and easy for users of all types.
