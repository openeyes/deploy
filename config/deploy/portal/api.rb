set :application, 'Portal_API'
set :repo_url, 'git@github.com:across-health/oe-community-portal-api.git'

set :linked_files, fetch(:linked_files, []).push('api/.env')
set :linked_dirs, fetch(:linked_dirs, []).push('api/storage')

# Flags to add to artisan calls.
set :laravel_artisan_flags, '--env=production'

Rake::Task['deploy:updated'].prerequisites.delete('npm:install')
Rake::Task['deploy:updated'].prerequisites.delete('bower:install')

set :composer_install_flags, -> { "--no-dev --prefer-dist --no-interaction  --quiet --optimize-autoloader --working-dir=#{fetch(:release_path)}/api" }

SSHKit.config.command_map[:artisan] = 'php api/artisan'

namespace :laravel do
  desc 'Execute a provided artisan command'
  task :artisan, :command_name do |_t, args|
    # ask only runs if argument is not provided
    ask(:cmd, 'list')
    command = args[:command_name] || fetch(:cmd)

    on roles(:web) do
      within release_path do
        execute :artisan, command, *args.extras, fetch(:laravel_artisan_flags)
      end
    end
  end

  task :optimize do
    on roles(:web) do
      within release_path do
        invoke 'laravel:artisan', 'optimize'
      end
    end
  end
end

namespace :deploy do
  task :migrate do
    on roles(:web) do
      within release_path do
        invoke 'laravel:artisan', 'migrate', '--force'
      end
    end
  end

  task :create_storage do
    on roles(:web) do
      execute :mkdir, '-p', "#{release_path}/api/storage/{app,logs,framework}"
      execute :chmod, '775', "#{release_path}/api/storage/{app,logs,framework}"
      execute :chmod, '775', "#{release_path}/api/bootstrap/cache"
    end
  end

  task :restart_php do
    on roles(:web) do
      execute :sudo, :service, 'php5-fpm restart'
    end
  end


  before 'deploy:updated', 'deploy:create_storage'
  before 'deploy:updated', 'laravel:optimize'
  before 'deploy:updated', 'deploy:migrate'

  after 'deploy:symlink:release', 'deploy:restart_php'
end