set :application, 'OpenEyes'
set :repo_url, 'https://github.com/openeyes/OpenEyes.git'

set :linked_files, fetch(:linked_files, []).push('.htaccess')
set :linked_dirs, fetch(:linked_dirs, []).push('protected/config/local', 'protected/runtime', 'protected/files',  'protected/modules/eyedraw')

Rake::Task['deploy:updated'].prerequisites.delete('npm:install')
Rake::Task['deploy:updated'].prerequisites.delete('bower:install')


SSHKit.config.command_map[:yiic] = "protected/yiic"


namespace :deploy do

  desc "Yii doesn't much like it when index.php is linked from shared so lets copy it to release"
  task :copy_files do
    on roles(:web) do
      execute :cp, "#{shared_path}/index.php", "#{release_path}/index.php"
    end
  end

  task :migrate do
    on roles(:web) do
      within release_path do
        execute :yiic, :migrate, "--interactive=0"
        execute :yiic, :migratemodules, "--interactive=0"
      end
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

  task :set_perm do
    on roles(:web) do
      execute :mkdir,  "#{release_path}/assets"
      execute :chmod, "-R", "777", "#{release_path}/assets"
      execute :chmod, "777", "#{release_path}/protected/files"
      execute :chmod, "777", "#{release_path}/protected/runtime"
    end
  end


  before 'deploy:updated', 'deploy:set_perm'
  before 'deploy:updated', 'deploy:copy_files'
  before 'deploy:updated', 'deploy:migrate'

  after 'deploy:symlink:release', 'deploy:restart_php'
end
