source 'https://rubygems.org'

require 'json'
require 'open-uri'
versions = JSON.parse(open('https://pages.github.com/versions.json').read)

group :development do
  gem 'stringex', '~> 1.4.0'
end

gem 'github-pages', versions['github-pages']
