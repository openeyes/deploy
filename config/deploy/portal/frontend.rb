set :gulp_tasks, 'build'
set :npm_flags, '--silent --no-progress'

set :linked_dirs, fetch(:linked_dirs, []).push('node_modules',)

Rake::Task['deploy:updated'].prerequisites.delete('composer:install')

namespace :deploy do
  before 'deploy:updated', 'npm:install'
  before 'deploy:updated', 'gulp'
end