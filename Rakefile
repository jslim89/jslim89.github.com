require "rubygems"
require "bundler/setup"

# This will be configured for you when you run config_deploy
deploy_branch  = "master"

## -- Misc Configs -- ##

public_dir      = "_site"    # compiled site directory
source_dir      = "_source"    # source file directory
deploy_dir      = "_deploy"   # deploy directory (for Github pages deployment)
server_port     = "4000"      # port for preview server eg. localhost:4000


#######################
# Working with Jekyll #
#######################

desc "Generate jekyll site"
task :generate do
  raise "### You haven't set anything up yet. First run `rake install` to set up an Octopress theme." unless File.directory?(source_dir)
  puts "## Generating Site with Jekyll"
  system "bundle exec jekyll build"
end

desc "preview the site in a web browser"
task :preview do
  raise "### You haven't set anything up yet. First run `rake install` to set up an Octopress theme." unless File.directory?(source_dir)
  puts "Starting to watch source with Jekyll and Compass. Starting Rack on port #{server_port}"
  system "bundle exec jekyll serve"
end


##############
# Deploying  #
##############

desc "Default deploy task"
task :deploy do
  system "cd #{public_dir} && git add * && git commit -am 'Updated on #{Time.now.strftime("%Y-%m-%d %H:%M")}' && git push origin master"
end
