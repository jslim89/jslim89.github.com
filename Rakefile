require "rubygems"
require "bundler/setup"
require "stringex"

# This will be configured for you when you run config_deploy
deploy_branch  = "master"

## -- Misc Configs -- ##

public_dir      = "_site"    # compiled site directory
source_dir      = "_source"    # source file directory
deploy_dir      = "_deploy"   # deploy directory (for Github pages deployment)
posts_dir       = "_posts"    # directory for blog files
server_port     = "4000"      # port for preview server eg. localhost:4000

new_post_ext    = "markdown"  # default new post file extension when using the new_post task


#######################
# Working with Jekyll #
#######################

# usage rake new_post[my-new-post] or rake new_post['my new post'] or rake new_post (defaults to "new-post")
desc "Begin a new post in #{source_dir}/#{posts_dir}"
task :new_post, :title do |t, args|
  if args.title
    title = args.title
  else
    title = get_stdin("Enter a title for your post: ")
  end
  raise "### You haven't set anything up yet. First run `rake install` to set up an Octopress theme." unless File.directory?(source_dir)
  mkdir_p "#{source_dir}/#{posts_dir}"
  filename = "#{source_dir}/#{posts_dir}/#{Time.now.strftime('%Y-%m-%d')}-#{title.to_url}.#{new_post_ext}"
  if File.exist?(filename)
    abort("rake aborted!") if ask("#{filename} already exists. Do you want to overwrite?", ['y', 'n']) == 'n'
  end
  puts "Creating new post: #{filename}"
  open(filename, 'w') do |post|
    post.puts "---"
    post.puts "layout: post"
    post.puts "title: \"#{title.gsub(/&/,'&amp;')}\""
    post.puts "date: #{Time.now.strftime('%Y-%m-%d %H:%M:%S %z')}"
    post.puts "comments: true"
    post.puts "categories: "
    post.puts "---"
  end
end

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
  system "cd #{public_dir} && git add * && git commit -am 'Updated on #{Time.now.strftime("%Y-%m-%d %H:%M")}' && git push -f origin master"
end
