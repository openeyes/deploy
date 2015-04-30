set :stage, :staging

server '130.1.27.143',
  user: 'deploy',
  roles: %w{web}

set :deploy_to, '/var/www/staging'
set :branch, ENV.fetch('branch', 'master')
set :meh, true
