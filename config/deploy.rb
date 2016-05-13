# config valid only for current version of Capistrano
lock '3.5.0'

set :scm, :git


# Default value for default_env is {}
# set :default_env, { path: "/opt/ruby/bin:$PATH" }

# Default value for keep_releases is 5
# set :keep_releases, 5

namespace :deploy do
  task :restart_php do
    on roles(:web) do
      execute :sudo, :service, 'php5-fpm restart'
    end
  end
end