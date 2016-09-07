set :stage, :staging

server 'release-api.acrossopeneyes.com',
  user: 'deploy',
  roles: %w{web}

set :deploy_to, '/var/www/portal_api'
set :branch, ENV.fetch('branch', 'master')
