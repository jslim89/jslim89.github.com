---
layout: post
title: "OS X failed bundle install"
date: 2012-12-08 10:10
comments: true
categories: 
- os-x
- ruby
- octopress
---

When I try to setup octopress in OS X, I run `bundle install`, it always come out an error like:

```
$ bundle install
Fetching gem metadata from http://rubygems.org/.......
Fetching gem metadata from http://rubygems.org/..
/Users/jslim/.rvm/gems/ruby-1.9.3-p327@global/gems/bundler-1.2.3/lib/bundler.rb:263: warning: Insecure world writable dir /usr/local/bin in PATH, mode 040777
Using rake (0.9.2.2) 
Installing RedCloth (4.2.9) with native extensions 
Gem::Installer::ExtensionBuildError: ERROR: Failed to build gem native extension.

        /Users/jslim/.rvm/rubies/ruby-1.9.3-p327/bin/ruby extconf.rb 
checking for main() in -lc... *** extconf.rb failed ***
Could not create Makefile due to some reason, probably lack of
necessary libraries and/or headers.  Check the mkmf.log file for more
details.  You may need configuration options.

Provided configuration options:
    --with-opt-dir
    --without-opt-dir
    --with-opt-include
    --without-opt-include=${opt-dir}/include
    --with-opt-lib
    --without-opt-lib=${opt-dir}/lib
    --with-make-prog
    --without-make-prog
    --srcdir=.
    --curdir
    --ruby=/Users/jslim/.rvm/rubies/ruby-1.9.3-p327/bin/ruby
    --with-redcloth_scan-dir
    --without-redcloth_scan-dir
    --with-redcloth_scan-include
    --without-redcloth_scan-include=${redcloth_scan-dir}/include
    --with-redcloth_scan-lib
    --without-redcloth_scan-lib=${redcloth_scan-dir}/lib
    --with-clib
    --without-clib
/Users/jslim/.rvm/rubies/ruby-1.9.3-p327/lib/ruby/1.9.1/mkmf.rb:369:in `try_do': The compiler failed to generate an executable file. (RuntimeError)
You have to install development tools first.
    from /Users/jslim/.rvm/rubies/ruby-1.9.3-p327/lib/ruby/1.9.1/mkmf.rb:449:in `try_link0'
    from /Users/jslim/.rvm/rubies/ruby-1.9.3-p327/lib/ruby/1.9.1/mkmf.rb:464:in `try_link'
    from /Users/jslim/.rvm/rubies/ruby-1.9.3-p327/lib/ruby/1.9.1/mkmf.rb:607:in `try_func'
    from /Users/jslim/.rvm/rubies/ruby-1.9.3-p327/lib/ruby/1.9.1/mkmf.rb:833:in `block in have_library'
    from /Users/jslim/.rvm/rubies/ruby-1.9.3-p327/lib/ruby/1.9.1/mkmf.rb:778:in `block in checking_for'
    from /Users/jslim/.rvm/rubies/ruby-1.9.3-p327/lib/ruby/1.9.1/mkmf.rb:272:in `block (2 levels) in postpone'
    from /Users/jslim/.rvm/rubies/ruby-1.9.3-p327/lib/ruby/1.9.1/mkmf.rb:242:in `open'
    from /Users/jslim/.rvm/rubies/ruby-1.9.3-p327/lib/ruby/1.9.1/mkmf.rb:272:in `block in postpone'
    from /Users/jslim/.rvm/rubies/ruby-1.9.3-p327/lib/ruby/1.9.1/mkmf.rb:242:in `open'
    from /Users/jslim/.rvm/rubies/ruby-1.9.3-p327/lib/ruby/1.9.1/mkmf.rb:268:in `postpone'
    from /Users/jslim/.rvm/rubies/ruby-1.9.3-p327/lib/ruby/1.9.1/mkmf.rb:777:in `checking_for'
    from /Users/jslim/.rvm/rubies/ruby-1.9.3-p327/lib/ruby/1.9.1/mkmf.rb:828:in `have_library'
    from extconf.rb:5:in `<main>'


Gem files will remain installed in /Users/jslim/.rvm/gems/ruby-1.9.3-p327/gems/RedCloth-4.2.9 for inspection.
Results logged to /Users/jslim/.rvm/gems/ruby-1.9.3-p327/gems/RedCloth-4.2.9/ext/redcloth_scan/gem_make.out
An error occurred while installing RedCloth (4.2.9), and Bundler cannot continue.
Make sure that `gem install RedCloth -v '4.2.9'` succeeds before bundling.
```

First, run

```
$ sudo xcodebuild -license
$ sudo rvm reinstall 1.9.3
```

Now you should be able to perform `bundle install`

```
$ cd /path/to/your/Gemfile
$ bundle install
```

It should work :)

_Reference: [The compiler failed to generate an executable file. (RuntimeError)](http://stackoverflow.com/questions/13279856/the-compiler-failed-to-generate-an-executable-file-runtimeerror#answers)_
