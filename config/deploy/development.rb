set :stage, :develop

server 'develop.openeyes.org.uk',
  user: 'deploy',
  roles: %w{web}

set :deploy_to, '/var/www/openeyestest'
set :branch, 'develop'
