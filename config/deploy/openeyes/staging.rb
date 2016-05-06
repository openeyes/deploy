set :stage, :staging

server 'develop.openeyes.org.uk',
  user: 'deploy',
  roles: %w{web}

set :deploy_to, '/var/www/openeyes_staging'
set :branch, ENV.fetch('branch', 'master')
