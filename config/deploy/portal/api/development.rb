set :stage, :develop

server 'dev-api.acrossopeneyes.com',
       user: 'deploy',
       roles: %w{web}

set :deploy_to, '/var/www/portal_api'
set :branch, ENV.fetch('branch', 'develop')
set :deploy_subdir, 'api'
