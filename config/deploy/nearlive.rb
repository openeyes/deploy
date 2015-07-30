set :stage, :nearlive

server 'nearlive.oe.moorfields.nhs.uk',
  user: 'deploy',
  roles: %w{web}

set :deploy_to, '/var/www/openeyestraining'
set :branch, ENV.fetch('branch', 'master')
