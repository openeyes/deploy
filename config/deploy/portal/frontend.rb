set :application, 'Portal_Frontend'
set :repo_url, 'git@github.com:across-health/oe-community-portal.git'

set :npm_flags, '--silent --no-progress'
set :env, ->{ fetch(:gulp_env) }

set :linked_dirs, fetch(:linked_dirs, []).push('node_modules',)

Rake::Task['deploy:updated'].prerequisites.delete('composer:install')

namespace :deploy do

  desc 'Run the NPM build'
  task :npm_build do
    on roles(:web) do
      within release_path do
        execute :npm, 'run build', "-- \"--env=#{fetch(:env)}\""
      end
    end
  end
  task :bower do
    on roles(:web) do
      within release_path do
        execute :npm, 'run bower'
      end
    end
  end
  before 'deploy:updated', 'npm:install'
  after 'npm:install', 'deploy:bower'
  after 'deploy:bower', 'deploy:npm_build'
end
