# config valid only for current version of Capistrano
lock '3.4.0'

set :application, 'OpenEyes'
set :repo_url, 'https://github.com/openeyes/OpenEyes.git'
set :scm, :git


# Default value for :linked_files is []
# set :linked_files, fetch(:linked_files, []).push('config/database.yml', 'config/secrets.yml')

# Default value for linked_dirs is []
# set :linked_dirs, fetch(:linked_dirs, []).push('log', 'tmp/pids', 'tmp/cache', 'tmp/sockets', 'vendor/bundle', 'public/system')

set :linked_files, fetch(:linked_files, []).push('.htaccess')
set :linked_dirs, fetch(:linked_dirs, []).push('protected/config/local', 'protected/runtime', 'protected/modules')

# Default value for default_env is {}
# set :default_env, { path: "/opt/ruby/bin:$PATH" }

# Default value for keep_releases is 5
# set :keep_releases, 5

namespace :deploy do

  desc "Yii doesn't much like it when index.php is linked from shared so lets copy it to release"
  task :copy_files do
    on roles(:web) do
      execute :cp, "#{shared_path}/index.php", "#{release_path}/index.php"
    end
  end

  after :restart, :clear_cache do
    on roles(:web), in: :groups, limit: 3, wait: 10 do
      # Here we can do anything such as:
      # within release_path do
      #   execute :rake, 'cache:clear'
      # end
    end
  end

  before 'deploy:updated', 'deploy:copy_files'
end
