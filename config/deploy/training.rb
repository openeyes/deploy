set :stage, :training

server 'training.openeyes.org.uk',
  user: 'deploy',
  roles: %w{web}

set :deploy_to, '/var/www/openeyestraining'
set :branch, ENV.fetch('branch', 'master')
